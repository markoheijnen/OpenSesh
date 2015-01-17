<?php

class OpenSesh_Organizer {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2);

		add_filter( 'enter_title_here', array( $this, 'change_enter_here_title' ) );

	}

	public function register_post_type() {
		// Organizer post type labels.
		$labels = array(
			'name'                  => __( 'Organizers', 'opensesh' ),
			'singular_name'         => __( 'Organizer', 'opensesh' ),
			'add_new'               => __( 'Add New', 'opensesh' ),
			'add_new_item'          => __( 'Create New Organizer', 'opensesh' ),
			'edit'                  => __( 'Edit', 'opensesh' ),
			'edit_item'             => __( 'Edit Organizer', 'opensesh' ),
			'new_item'              => __( 'New Organizer', 'opensesh' ),
			'view'                  => __( 'View Organizer', 'opensesh' ),
			'view_item'             => __( 'View Organizer', 'opensesh' ),
			'search_items'          => __( 'Search Organizers', 'opensesh' ),
			'not_found'             => __( 'No organizers found', 'opensesh' ),
			'not_found_in_trash'    => __( 'No organizers found in Trash', 'opensesh' ),
			'parent_item_colon'     => __( 'Parent Organizer:', 'opensesh' )
		);

		// Register organizer post type.
		register_post_type( 'organizer', array(
			'labels'            => $labels,
			'rewrite'           => array( 'slug' => 'organizer', 'with_front' => false ),
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

	public function change_enter_here_title( $title ) {
		$screen = get_current_screen();

		if ( $screen-> post_type == 'organizer') {
			return __('Enter Organizer name here (First and Last)', 'opensesh');
		}

		return $title;
	}


	/**
	 * Fired during add_meta_boxes, adds extra meta boxes to our custom post types.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'organizer-info', __( 'Organizer Info', 'opensesh' ), array( $this, 'metabox_organizer_info' ), 'organizer', 'side' );
	}

	/**
	 * Rendered in the Organizer post type
	 */
	public function metabox_organizer_info( $post ) {
		$twitter = get_post_meta( $post->ID, '_twitter', true );
		?>

		<?php wp_nonce_field( 'edit-organizer-info', 'meta-organizer-info' ); ?>

		<p>
			<label for="organizer-twitter"><?php _e( 'Twitter Username:', 'opensesh' ); ?></label>
			<input type="text" class="widefat" id="organizer-twitter" name="organizer-twitter" value="<?php echo esc_attr( $twitter ); ?>" />
		</p>

	<?php
	}

	/**
	 * When an Organizer post is saved, update some meta data.
	 */
	public function save_meta_box( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || $post->post_type != 'organizer' || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['meta-organizer-info'] ) && wp_verify_nonce( $_POST['meta-organizer-info'], 'edit-organizer-info' ) ) {
			$twitter = sanitize_text_field( $_POST['organizer-twitter'] );

			update_post_meta( $post_id, '_twitter', $twitter );
		}
	}
}