var config = {}
  , channels = {}
  , clients = [];

config.port = 9000;
config.application_host = 'http://opensesh.dev';
config.application_port = 80;

var app = require('http').createServer(handler),
	io = require('socket.io').listen(app),
	wordpress = require("WordPress-Utils"),
	fs = require('fs');

app.listen(config.port);

wordpress.set_siteurl( config.application_host );

load_current_state();

process.on('SIGINT', function (err) {
	save_current_state();
});
process.on('uncaughtException', function (err) {
	save_current_state();
});


io.on('connection', function (socket) {
	clients[socket.id] = socket;

	socket.url  = get_user_domain( socket.handshake.headers );
	socket.wp_user = wordpress.connect( socket.handshake.headers.cookie, socket.handshake.query.token );

	if ( socket.wp_user ) {
		// When socket disconnects, remove it from the list:
		socket.on('disconnect', function() {
			delete clients[socket.id];
		});

		var channel_id  = parseInt(socket.handshake.query.channel);
		var channel_url = get_channel( socket.handshake.headers, channel_id );

		if ( channel_url ) {
			socket.emit('publish', { channel: channel_id, url: channel_url });
		}
	}
});

function handler(req, res) {
	if (req.url == "/status") {
		res.writeHead( 200 );
		res.end();
		return;
	}

	wordpress.set_siteurl( get_user_domain( req.headers ) );
	var wp_user = wordpress.connect( req.headers.cookie, req.headers['x-token'] );

	if ( wp_user ) {
		wp_user.on('wp_connected', function ( data ) {
			if ( ! wp_user.can('manage_options') ) {
				res.writeHead(403);
				res.end();
				return;
			}

			var fullBody = '';

			req.on('data', function(chunk) {
				fullBody += chunk.toString();
			});

			req.on('end', function() {
				if (req.url == "/publish") {
					var json = JSON.parse(fullBody);

					if ( json.channel && json.url ) {
						update_channel( req.headers, json.channel, json.url )

						send_message( get_user_domain(req.headers), 'publish', { channel : json.channel, url : json.url });
					}
					else {
						res.writeHead(404);
					}
					res.end();
				}
				else if (req.url == "/channels") {
					res.writeHead( 200, {'Content-Type': 'application/json'} );
					res.end( JSON.stringify( get_channels( req.headers ) ) );
				}
			});
		});

		wp_user.on('wp_connect_failed', function ( data ) {
			res.writeHead(401);
			res.end();
		});
	}
	else {
		res.writeHead(500);
		res.end();
	}
}




function load_current_state() {
	fs.readFile('cache-channels', function (err, data) {
		if (err) {
			return;
		}
		
		try {
			channels = JSON.parse(data);
		} catch (e) {
			return false;
		}
	});
}
function save_current_state() {
	fs.writeFile('cache-channels', JSON.stringify(channels), function(err) {
		if (err) {
			throw err;
		}

		process.exit();
	});
}



function send_message( domain, name, data ) {
	for (client in clients) {
		client = clients[client];

		if ( client.url == domain ) {
			client.emit( name, data );
		}
	}

	//io.sockets.emit( name, data );
}



function get_channel( headers, channel ) {
	if ( channels[ get_user_domain(headers) ] && channels[ get_user_domain(headers) ][ channel ] ) {
		return channels[ get_user_domain(headers) ][ channel ].url;
	}

	return false;
}

function get_channels( headers ) {
	return channels[ get_user_domain(headers) ];
}

function update_channel( headers, channel, url ) {
	if ( ! channels[ get_user_domain(headers) ] ) {
		channels[ get_user_domain(headers) ] = [];
	}

	if ( ! channels[ get_user_domain(headers) ][ channel ] ) {
		channels[ get_user_domain(headers) ][ channel ] = {};
	}

	channels[ get_user_domain(headers) ][ channel ].url = url;
}




function get_user_domain(headers) {
	return 'http://' + headers.host.slice(0, headers.host.indexOf(':') );
}