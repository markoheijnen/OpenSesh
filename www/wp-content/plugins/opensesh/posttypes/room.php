<?php

class OpenSesh_Room {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

	}

	public function register_post_type() {
		// Organizer post type labels.
		$labels = array(
			'name'                  => __( 'Rooms', 'opensesh' ),
			'singular_name'         => __( 'Room', 'opensesh' ),
			'add_new'               => __( 'Add New', 'opensesh' ),
			'add_new_item'          => __( 'Create New Room', 'opensesh' ),
			'edit'                  => __( 'Edit', 'opensesh' ),
			'edit_item'             => __( 'Edit Room', 'opensesh' ),
			'new_item'              => __( 'New Room', 'opensesh' ),
			'view'                  => __( 'View Room', 'opensesh' ),
			'view_item'             => __( 'View Room', 'opensesh' ),
			'search_items'          => __( 'Search Rooms', 'opensesh' ),
			'not_found'             => __( 'No rooms found', 'opensesh' ),
			'not_found_in_trash'    => __( 'No rooms found in Trash', 'opensesh' ),
			'parent_item_colon'     => __( 'Parent Rooms:', 'opensesh' )
		);

		// Register Rooms post type.
		register_post_type( 'rooms', array(
			'labels'            => $labels,
			'rewrite'           => array( 'slug' => 'rooms', 'with_front' => false ),
			'supports'          => array( 'title', 'editor', 'revisions' ),
			'menu_position'     => 22,
			'public'            => false,
			'show_ui'           => true,
			'can_export'        => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-admin-users'
		) );
	}




}