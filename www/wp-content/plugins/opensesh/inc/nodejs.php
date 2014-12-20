<?php

class OpenSesh_Nodejs {
	private static $status = null;
	private static $url    = null;

	public function __construct() {
		self::$url = home_url() . ':9000/';

		add_shortcode( 'channel', array( $this, 'do_shortcode' ) );
	}


	public function do_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'number' => false
		), $atts ) );

		if ( $number ) {
			$this->load_script();

			return '<div class="channel-movie" data-channel="' . $number . '">' . __( 'Currently there is no live session.', 'opensesh' ) . '</div>';
		}
	}


	public static function get_url() {
		return self::$url;
	}

	public static function status() {
		if ( self::$status !== null ) {
			return self::$status;
		}

		self::$status = false;

		$args = array(
			'headers' => array(
				'content-type' => 'application/json',
				'x-token' => wp_create_nonce( 'wp_json' ),
			),
			'cookies' => $_COOKIE,
			'timeout' => 1,
		);
		$response = wp_remote_get( self::$url . 'status', $args );

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			self::$status = true;
		}

		return self::$status;
	}

	public static function channels() {
		if ( ! self::status() ) {
			return false;
		}

		$args = array(
			'headers' => array(
				'content-type' => 'application/json',
				'x-token' => wp_create_nonce( 'wp_json' ),
			),
			'cookies' => $_COOKIE,
		);
		$response = wp_remote_get( self::$url . 'channels', $args );

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			return json_decode( wp_remote_retrieve_body( $response ) );
		}

		return false;
	}

	public static function publish( $channel, $url ) {
		if ( ! self::status() ) {
			return false;
		}

		$args = array(
			'headers' => array(
				'content-type' => 'application/json',
				'x-token' => wp_create_nonce( 'wp_json' ),
			),
			'cookies' => $_COOKIE,
			'body' => json_encode(
				array(
					'channel' => $channel,
					'url'     => $url
				)
			),
		);
		$response = wp_remote_post( self::$url . 'publish', $args );

		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			return true;
		}

		return false;
	}


	private function load_script() {
		wp_enqueue_script( 'socket.io', self::$url . 'socket.io/socket.io.js' );
		wp_enqueue_script( 'frontend', plugins_url( 'js/frontend.js', dirname( __FILE__ ) ), array( 'socket.io' ) );

		wp_localize_script( 'frontend', 'wp_nodejs', array(
			'url'    => home_url(),
			'nodejs' => self::get_url(),
 			'nonce'  => wp_create_nonce( 'wp_json' ),
		) );
	}

}

