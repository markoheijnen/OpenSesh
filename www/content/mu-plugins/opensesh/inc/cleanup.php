<?php

class OpenSesh_Cleanup {

	private $not_allowed_pages = array();

	public function __construct() {
		// Main stuff to let it work
		add_action( 'current_screen', array( $this, '_make_pages_inaccessible' ) );
		add_action( 'admin_menu', array( $this, 'start_removing' ), 0 );

		// Remove default widgets from WordPress
		add_action( 'wp_dashboard_setup', array( $this, '_remove_dashboard_widgets' ) );
		//add_action( 'widgets_init', array( $this, '_remove_default_widgets' ) );

		// Clean up the profile
		add_action( 'admin_head', array( $this, '_remove_unneeded_profilefields' ) );
		add_filter( 'user_contactmethods', array( $this, '_remove_default_contactmethods' ) );
	}

	public function start_removing() {
		$this->remove_my_sites();
		$this->remove_tools();
		//$this->remove_settings();
	}

	public function remove_my_sites() {
		add_action( 'admin_menu', array( $this, '_remove_my_sites' ) );
		$this->not_allowed_pages[] = 'my-sites.php';
	}

	public function remove_comments() {
		add_action( 'admin_menu', array( $this, '_remove_comments' ) );

		$this->not_allowed_pages[] = 'edit-comments';
	}

	public function remove_tools() {
		add_action( 'admin_menu', array( $this, '_remove_tools' ) );

		$this->not_allowed_pages[] = 'tools';
		$this->not_allowed_pages[] = 'import';
		$this->not_allowed_pages[] = 'export';
		$this->not_allowed_pages[] = 'ms-delete-site';
	}

	public function remove_settings() {
		if ( is_super_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, '_remove_settings' ) );

		$this->not_allowed_pages[] = 'options-writing';
		$this->not_allowed_pages[] = 'options-reading';
		$this->not_allowed_pages[] = 'options-discussion';
		$this->not_allowed_pages[] = 'ptions-media';
		$this->not_allowed_pages[] = 'options-privacy';
		$this->not_allowed_pages[] = 'options-permalink';
		
	}



	/*
	 * Methods that should be called from an action. Should be handled as private
	 *
	 */

	function _make_pages_inaccessible() {
		$current_screen = get_current_screen();

		if ( in_array( esc_attr( $current_screen->base ), $this->not_allowed_pages ) ) {
			wp_redirect( admin_url( '/' ) );
		}
	}

	function _remove_dashboard_widgets() {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );

		//remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		//remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );

		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );

		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	}

	function _remove_default_widgets() {
		unregister_widget( 'WP_Nav_Menu_Widget' );
		unregister_widget( 'WP_Widget_Meta' );
		unregister_widget( 'WP_Widget_Recent_Comments' );
		unregister_widget( 'WP_Widget_RSS' );
		unregister_widget( 'WP_Widget_Text' );

		unregister_widget( 'WP_Widget_Pages' );
		unregister_widget( 'WP_Widget_Calendar' );
		unregister_widget( 'WP_Widget_Archives' );
		unregister_widget( 'WP_Widget_Links' );
		unregister_widget( 'WP_Widget_Categories' );
		unregister_widget( 'WP_Widget_Recent_Posts' );
		unregister_widget( 'WP_Widget_Search' );
		unregister_widget( 'WP_Widget_Tag_Cloud' );
	}


	public function _remove_my_sites() {
		remove_submenu_page( 'index.php', 'my-sites.php' );
	}

	public function _remove_tools() {
		remove_menu_page( 'tools.php' );

		remove_submenu_page( 'tools.php', 'tools.php' );
		remove_submenu_page( 'tools.php', 'import.php' );
		remove_submenu_page( 'tools.php', 'export.php' );
		remove_submenu_page( 'tools.php', 'ms-delete-site.php' );
	}

	public function _remove_settings() {
		remove_submenu_page( 'options-general.php', 'options-writing.php' );
		remove_submenu_page( 'options-general.php', 'options-reading.php' );
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		remove_submenu_page( 'options-general.php', 'options-media.php' );
		remove_submenu_page( 'options-general.php', 'options-privacy.php' );
		remove_submenu_page( 'options-general.php', 'options-permalink.php' );
	}

	public function _remove_unneeded_profilefields() {
		if( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
			remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

			global $wp_rich_edit_exists;
			$wp_rich_edit_exists = false;

			global $user_can_edit;
			$user_can_edit = false;
		}
	}

	public function _remove_default_contactmethods( $user_contactmethods ) {
		unset( $user_contactmethods['aim'] );
		unset( $user_contactmethods['yim'] );
		unset( $user_contactmethods['jabber'] );

		return $user_contactmethods;
	}

}