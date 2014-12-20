jQuery( document ).ready(function( $ ) {
	$('.youtube-search button').css( 'border-color', $('.youtube-search button').css( 'background-color' ) );

	$('.youtube-search button').on('hover blur', function() {
		$('.youtube-search button').css( 'border-color', $('.youtube-search button').css( 'background-color' ) );
	});


	$('.youtube-search button').click(function(evt) {
		evt.preventDefault();

		var data = {
			action: 'youtube_search',
			keyword: $('.youtube-search-input input').val()
		};

		var holder = $('.themes');

		$.post(ajaxurl, data, function(response) {
			holder.html('');

			$.each(response.data.items, function(i, item) {
				var html = '<div class="theme" data-id="' + item.id + '" data-mobile="' + item.mobile + '">';
					html += '<div class="theme-screenshot">';
					html += '<img src="' + item.thumbnail.high + '" alt="">';
					html += '</div>';

					html += '<span class="more-details">' + item.title + '</span>';

					html += '<h3 class="theme-name">' + item.title + '</h3>';

					html += '<div class="theme-actions">';
					if( ! response.data.nodejs ) {
						//html += '<span class="play button button-primary button-primary-disabled" href="#">Play</span>';
					}
					else {
						html += '<a class="button button-primary play" href="" data-channels="1,2">All</a>';
						html += '<a class="button button-primary play" href="" data-channels="1">Channel 1</a>';
						html += '<a class="button button-primary play" href="" data-channels="2">Channel 2</a>';
					}
					html += '</div>';
					html += '</div>';

				holder.append( html );
			});
		});
	});


	$('.channel-submit').click(function(evt) {
		evt.preventDefault();

		var button = $(this);
		var url    = button.parent().find('.channel-link').val();

		opensesh_play( button.data('channel'), url );
	});

	$(document).on('click', '.theme-actions .play', function(evt) {
		evt.preventDefault();

		var channels = new String( $(this).data('channels') ).split(',');

		var url = 'www.youtube.com/embed/' + $(this).closest('.theme').data('id');

		$.each( channels, function( key, channel ) {
			opensesh_play( channel, url );
		});
	});


	function opensesh_play( channel, url ) {
		if ( ! /^https?:\/\//i.test( url ) ) {
		    url = 'http://' + url;
		}

		var data = {
			action: 'nodejs_play',
			channel: channel,
			url: url
		};

		$.post(ajaxurl, data, function(response) {
			if( response.success ) {
				$('#channel-' + channel + ' .channel-movie iframe').attr('src', url);
			}
		});
	}

});