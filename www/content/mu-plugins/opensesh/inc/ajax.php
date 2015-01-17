<?php

class OpenSesh_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_youtube_search', array( $this, 'youtube_search' ) );
		add_action( 'wp_ajax_nodejs_play', array( $this, 'nodejs_play' ) );
	}

	public function youtube_search() {
		if( isset( $_POST['keyword'] ) ) {
			$keyword          = sanitize_text_field( $_POST['keyword'] );
			$movies           = OpenSesh_Youtube::search( $keyword );
			$movies['nodejs'] = OpenSesh_Nodejs::status();

			wp_send_json_success( $movies );
		}

		wp_send_json_error();
	}


	public function nodejs_play() {
		if ( isset( $_POST['channel'], $_POST['url'] ) ) {
			if ( OpenSesh_Nodejs::publish( absint( $_POST['channel'] ), esc_url( $_POST['url'] ) ) ) {
				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}

}

