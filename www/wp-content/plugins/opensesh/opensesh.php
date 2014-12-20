<?php
/*
Plugin Name: 	OpenSesh
Description: 	
Version: 		1.0
Plugin URI: 	
Author: 		Marko Heijnen
*/

class OpenSesh {
	public static $amount_channels = 2;
	private $modules = array();

	public function __construct() {
		if ( is_multisite() && is_main_site() && defined( 'MULTISITE' ) ) {
			return;
		}

		if ( is_admin() ) {
			include 'inc/admin.php';
			include 'inc/ajax.php';
			include 'inc/cleanup.php';
			include 'inc/google.php';
			include 'inc/youtube.php';

			$modules['admin']   = new OpenSesh_Admin;
			$modules['ajax']    = new OpenSesh_Ajax;
			$modules['cleanup'] = new OpenSesh_Cleanup;
			$modules['google']  = new OpenSesh_Google;
			$modules['youtube'] = new OpenSesh_Youtube;
		}

		include 'inc/nodejs.php';
		$modules['nodejs'] = new OpenSesh_Nodejs;

		$this->load_post_types();

		add_action( 'init', array( $this, 'register_styles' ) );
		add_action( 'init', array( $this, 'register_scripts' ) );
	}


	public function register_styles() {
		wp_register_style( 'opensesh-admin', plugins_url( 'css/admin.css', __FILE__ ) );
	}

	public function register_scripts() {
		wp_register_script( 'opensesh-admin', plugins_url( 'js/admin.js', __FILE__ ) );
	}


	private function load_post_types() {
		include 'posttypes/organizer.php';
		include 'posttypes/session.php';
		include 'posttypes/speaker.php';
		include 'posttypes/sponsor.php';
		include 'posttypes/room.php';
		include 'posttypes/edition.php';



		$modules['organizer'] = new OpenSesh_Organizer;
		$modules['session']   = new OpenSesh_Session;
		$modules['speaker']   = new OpenSesh_Speaker;
		$modules['sponsor']   = new OpenSesh_Sponsor;
		$modules['room']      = new OpenSesh_Room;
		$modules['edition']   = new OpenSesh_Edition;


	}

}

new OpenSesh;