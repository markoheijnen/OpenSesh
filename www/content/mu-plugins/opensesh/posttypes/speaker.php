<?php

class OpenSesh_Speaker {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		add_filter( 'enter_title_here', array( $this, 'change_enter_here_title' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2);
	}

	public function register_post_type() {
		// Speaker post type labels.
		$labels = array(
			'name'                  => __( 'Speakers', 'opensesh' ),
			'singular_name'         => __( 'Speaker', 'opensesh' ),
			'add_new'               => __( 'Add New', 'opensesh' ),
			'add_new_item'          => __( 'Create New Speaker', 'opensesh' ),
			'edit'                  => __( 'Edit', 'opensesh' ),
			'edit_item'             => __( 'Edit Speaker', 'opensesh' ),
			'new_item'              => __( 'New Speaker', 'opensesh' ),
			'view'                  => __( 'View Speaker', 'opensesh' ),
			'view_item'             => __( 'View Speaker', 'opensesh' ),
			'search_items'          => __( 'Search Speakers', 'opensesh' ),
			'not_found'             => __( 'No speakers found', 'opensesh' ),
			'not_found_in_trash'    => __( 'No speakers found in Trash', 'opensesh' ),
			'parent_item_colon'     => __( 'Parent Speaker:', 'opensesh' )
		);

		// Register speaker post type.
		register_post_type( 'speaker', array(
			'labels'            => $labels,
			'rewrite'           => array( 'slug' => 'speaker', 'with_front' => true ),
			'supports'          => array( 'title', 'editor', 'revisions' ),
			'menu_position'     => 20,
			'public'            => true,
			'show_ui'           => true,
			'can_export'        => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-welcome-learn-more'
		) );
	}

	public function change_enter_here_title( $title ) {
		$screen = get_current_screen();

		if ( $screen->post_type == 'speaker' ) {
			return __( 'Enter Speaker name here', 'opensesh' );
		}

		return $title;
	}


	/**
	 * Fired during add_meta_boxes, adds extra meta boxes to our custom post types.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'speaker-info', __( 'Speaker Info', 'opensesh' ), array( $this, 'metabox_speaker_info' ), 'speaker', 'side' );
	}

	/**
	 * Used by the Speakers post type
	 */
	public function metabox_speaker_info( $post ) {
		$email   = get_post_meta( $post->ID, '_email', true );
		$twitter = get_post_meta( $post->ID, 'twitter', true );
		?>

		<?php wp_nonce_field( 'edit-speaker-info', 'meta-speaker-info' ); ?>

		<p>
			<label for="speaker-gravatar-email"><?php _e( 'Gravatar Email:', 'opensesh' ); ?></label>
			<input type="text" class="widefat" placeholder="<?php _e( 'Your Gravatar Email', 'opensesh' ); ?>" id="speaker-gravatar-email" name="speaker-gravatar-email" value="<?php echo esc_attr( $email ); ?>" />
		</p>

		<p>
			<label for="speaker-gravatar-email"><?php _e( 'Twitter Handle:', 'opensesh' ); ?></label>
			<input type="text" class="widefat" placeholder="<?php _e( 'Your Twitter Handle', 'opensesh' ); ?>" id="speaker-twitter-handle" name="speaker-twitter-handle" value="<?php echo esc_attr( $twitter ); ?>" />
		</p>

	<?php
	}

	/**
	 * Fired when a post is saved, makes sure additional metadata is also updated.
	 */
	function save_meta_box( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || $post->post_type != 'speaker' || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['meta-speaker-info'] ) && wp_verify_nonce( $_POST['meta-speaker-info'], 'edit-speaker-info' ) ) {
			$email   = sanitize_text_field( $_POST['speaker-gravatar-email'] );
			$twitter = sanitize_text_field( $_POST['speaker-twitter-handle'] );

			update_post_meta( $post_id, 'twitter', $twitter );

			if ( empty( $email) ) {
				delete_post_meta( $post_id, '_email' );
			}
			elseif ( $email && is_email( $email ) ) {
				update_post_meta( $post_id, '_email', $email );
			}
		}
	}

}