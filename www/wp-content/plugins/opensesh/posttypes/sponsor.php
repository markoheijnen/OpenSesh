<?php

class OpenSesh_Sponsor {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		add_filter( 'enter_title_here', array( $this, 'change_enter_here_title' ) );


		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2);


	}

	public function register_post_type() {
		// Sponsor post type labels.
		$labels = array(
			'name'                  => __( 'Sponsors', 'opensesh' ),
			'singular_name'         => __( 'Sponsor', 'opensesh' ),
			'add_new'               => __( 'Add New', 'opensesh' ),
			'add_new_item'          => __( 'Create New Sponsor', 'opensesh' ),
			'edit'                  => __( 'Edit', 'opensesh' ),
			'edit_item'             => __( 'Edit Sponsor', 'opensesh' ),
			'new_item'              => __( 'New Sponsor', 'opensesh' ),
			'view'                  => __( 'View Sponsor', 'opensesh' ),
			'view_item'             => __( 'View Sponsor', 'opensesh' ),
			'search_items'          => __( 'Search Sponsors', 'opensesh' ),
			'not_found'             => __( 'No sponsors found', 'opensesh' ),
			'not_found_in_trash'    => __( 'No sponsors found in Trash', 'opensesh' ),
			'parent_item_colon'     => __( 'Parent Sponsor:', 'opensesh' )
		);

		// Register sponsor post type.
		register_post_type( 'sponsor', array(
			'labels'            => $labels,
			'rewrite'           => array( 'slug' => 'sponsor', 'with_front' => false ),
			'supports'          => array( 'title', 'editor', 'revisions', 'thumbnail' ),
			'menu_position'     => 21,
			'public'            => true,
			'show_ui'           => true,
			'can_export'        => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups'
		) );
	}

	public function change_enter_here_title( $title ) {
		$screen = get_current_screen();

		if ( $screen-> post_type == 'sponsor') {
			return __('Enter Sponsor name here', 'opensesh');
		}

		return $title;
	}


	/**
	 * Fired during add_meta_boxes, adds extra meta boxes to our custom post types.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'sponsor-info', __( 'Sponsor Info', 'opensesh' ), array( $this, 'metabox_sponsor_info' ), 'sponsor', 'side' );
	}

	/**
	 * Render the Sponsor Info metabox view
	 */
	public function metabox_sponsor_info( $sponsor ) {
		$website = get_post_meta( $sponsor->ID, '_website', true );
		$twitter = get_post_meta( $sponsor->ID, '_twitter_handle', true );

		wp_nonce_field( 'edit-sponsor-info', 'meta-sponsor-info' );

		?>

		<p>
			<label for="sponsor_website"><?php _e( 'Website:', 'opensesh' ); ?></label>
			<input type="text" class="widefat" id="sponsor_website" name="sponsor_website" value="<?php echo esc_attr( esc_url( $website ) ); ?>" />
		</p>

		<p>
			<label for="speaker-gravatar-email"><?php _e( 'Twitter Handle:', 'opensesh' ); ?></label>
			<input type="text" class="widefat" placeholder="Your Twitter Handle" id="speaker-twitter-handle" name="speaker-twitter-handle" value="<?php echo esc_attr( $twitter ); ?>" />
		</p>

	<?php
	}

	/**
	 * Save meta data for Sponsor posts
	 */
	public function save_meta_box( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || $post->post_type != 'sponsor' || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['meta-sponsor-info'] ) && wp_verify_nonce( $_POST['meta-sponsor-info'], 'edit-sponsor-info' ) ) {
			$website = esc_url_raw( $_POST['sponsor_website'] );

			$twitter = sanitize_text_field( $_POST['speaker-twitter-handle'] );

			if ( empty( $twitter)  ) {
				delete_post_meta( $post_id, '_twitter_handle' );

			}
			elseif ( $twitter ) {
				update_post_meta( $post_id, '_twitter_handle', $twitter );

			}

			if ( $website ) {
				update_post_meta( $post_id, '_website', $website );
			}
			else {
				delete_post_meta( $post_id, '_website' );
			}
		}
	}

}