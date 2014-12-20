<?php

class OpenSesh_Edition {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

	}

	public function register_post_type() {
		// Editions post type labels.
		$labels = array(
			'name'                  => __( 'Editions', 'opensesh' ),
			'singular_name'         => __( 'Edition', 'opensesh' ),
			'add_new'               => __( 'Add New', 'opensesh' ),
			'add_new_item'          => __( 'Create New Edition', 'opensesh' ),
			'edit'                  => __( 'Edit', 'opensesh' ),
			'edit_item'             => __( 'Edit Edition', 'opensesh' ),
			'new_item'              => __( 'New Edition', 'opensesh' ),
			'view'                  => __( 'View Edition', 'opensesh' ),
			'view_item'             => __( 'View Edition', 'opensesh' ),
			'search_items'          => __( 'Search Editions', 'opensesh' ),
			'not_found'             => __( 'No editions found', 'opensesh' ),
			'not_found_in_trash'    => __( 'No editions found in Trash', 'opensesh' ),
			'parent_item_colon'     => __( 'Parent Editions:', 'opensesh' )
		);

		// Register Editions post type.
		register_post_type( 'editions', array(
			'labels'            => $labels,
			'rewrite'           => array( 'slug' => 'editions', 'with_front' => false ),
			'supports'          => array( 'title', 'editor', 'revisions' ),
			'menu_position'     => 25,
			'public'            => false,
			'show_ui'           => true,
			'can_export'        => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-feedback'
		) );
	}




}