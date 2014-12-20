if ( typeof io !== "undefined" ) {
	var channel    = jQuery('.channel-movie');
	var channel_id = channel.data('channel');
	var socket     = io.connect( wp_nodejs.nodejs, { query: { token: wp_nodejs.nonce, channel: channel_id } });

	socket.on('publish', function (data) {
		if( channel_id == data.channel ) {
			if( data && data.url ) {
				reload_channel_html(data.url);
			}
			else {
				reload_channel_html('');
			}
		}
	});

	socket.on('connect', function (evt) {
		console.log('connected');
	});

	socket.on('disconnect', function () {
		console.log('disconnected');
		//reload_channel_html('');
	});


	function reload_channel_html( link ) {
		if( link ) {
			if( link.indexOf('youtube.com') ) {
				link += '?autoplay=1';
			}

			channel.html('<iframe width="100%" height="315" src="' + link + '" frameborder="0" allowfullscreen></iframe>');
		}
		else {
			channel.html('Currently there is no live session.');
		}
	}
}