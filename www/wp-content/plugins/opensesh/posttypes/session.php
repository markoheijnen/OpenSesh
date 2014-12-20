<?php

class OpenSesh_Session {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		add_filter( 'enter_title_here', array( $this, 'change_enter_here_title' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2);
	}

	public function register_post_type() {
		// Session post type labels.
		$labels = array(
			'name'                  => __( 'Sessions', 'opensesh' ),
			'singular_name'         => __( 'Session', 'opensesh' ),
			'add_new'               => __( 'Add New', 'opensesh' ),
			'add_new_item'          => __( 'Create New Session', 'opensesh' ),
			'edit'                  => __( 'Edit', 'opensesh' ),
			'edit_item'             => __( 'Edit Session', 'opensesh' ),
			'new_item'              => __( 'New Session', 'opensesh' ),
			'view'                  => __( 'View Session', 'opensesh' ),
			'view_item'             => __( 'View Session', 'opensesh' ),
			'search_items'          => __( 'Search Sessions', 'opensesh' ),
			'not_found'             => __( 'No sessions found', 'opensesh' ),
			'not_found_in_trash'    => __( 'No sessions found in Trash', 'opensesh' ),
			'parent_item_colon'     => __( 'Parent Session:', 'opensesh' )
		);

		// Register session post type.
		register_post_type( 'session', array(
			'labels'            => $labels,
			'rewrite'           => array( 'slug' => 'session', 'with_front' => false ),
			'supports'          => array( 'title', 'editor', 'revisions', 'thumbnail' ),
			'menu_position'     => 21,
			'public'            => true,
			'show_ui'           => true,
			'can_export'        => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-video-alt2'
		) );
	}

	public function change_enter_here_title( $title ) {
		$screen = get_current_screen();

		if ( $screen-> post_type == 'session') {
			return __('Enter Session name here', 'opensesh');
		}

		return $title;
	}


	/**
	 * Fired during add_meta_boxes, adds extra meta boxes to our custom post types.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'speakers-list', __( 'Speakers', 'opensesh' ), array( $this, 'metabox_speakers_list' ), 'session', 'side' );
		add_meta_box( 'session-info', __( 'Session Info', 'opensesh' ), array( $this, 'metabox_session_info' ), 'session', 'side' );
	}

	/**
	 * Used by the Sessions post type, renders a text box for speakers input.
	 */
	public function metabox_speakers_list( $post ) {
		$speakers = get_post_meta( $post->ID, '_wcb_session_speakers', true );
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		$speakers_names   = array();
		$speakers_objects = get_posts( array(
			'post_type'      => 'speaker',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );

		// We'll use these in js.
		foreach ( $speakers_objects as $speaker_object ) {
			$speakers_names[] = $speaker_object->post_title;
		}

		$speakers_names_first = array_pop( $speakers_names );
		?>

		<?php wp_nonce_field( 'edit-speakers-list', '-meta-speakers-list-nonce' ); ?>

		<textarea class="large-text" placeholder="Start typing a name" id="speakers-list" name="speakers-list"><?php echo esc_textarea( $speakers ); ?></textarea>

		<p class="description">
			<?php _e( 'A speaker entry must exist first. Separate multiple speakers with commas.', 'opensesh' ); ?>
		</p>

		<script>
			jQuery(document).ready( function($) {
				var availableSpeakers = [ <?php
				foreach ( $speakers_names as $name ) { printf( "'%s', ", esc_js( $name ) ); }
				printf( "'%s'", esc_js( $speakers_names_first ) ); // avoid the trailing comma
			?> ];
				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}
				$( '#wcpt-speakers-list' )
					.bind( 'keydown', function( event ) {
						if ( event.keyCode == $.ui.keyCode.TAB &&
							$( this ).data( 'autocomplete' ).menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 0,
						source: function( request, response ) {
							response( $.ui.autocomplete.filter(
								availableSpeakers, extractLast( request.term ) ) )
						},
						focus: function() {
							return false;
						},
						select: function( event, ui ) {
							var terms = split( this.value );
							terms.pop();
							terms.push( ui.item.value );
							terms.push( '' );
							this.value = terms.join( ', ' );
							$(this).focus();
							return false;
						},
						open: function() { $(this).addClass('open'); },
						close: function() { $(this).removeClass('open'); }
					});
			});
		</script>

	<?php
	}

	public function metabox_session_info( $post ) {
		$session_time = absint( get_post_meta( $post->ID, '_session_time', true ) );
		$session_time = ( $session_time ) ? date( 'Y-m-d H:i:s', $session_time ) : '';
		$session_type = get_post_meta( $post->ID, '_session_type', true );
		?>

		<?php wp_nonce_field( 'edit-session-info', 'meta-session-info' ); ?>

		<p>
			<label for="session-time"><?php _e( 'When:', 'opensesh' ); ?></label>
			<input type="text" class="widefat" id="session-time" name="session-time" value="<?php echo esc_attr( $session_time ); ?>" />
			<span style="display: block; margin-top: 4px;" class="description">For example: <?php echo date( 'Y-m-d H:i:s' ); ?></span>
		</p>

		<p>
			<label for="session-type"><?php _e( 'Type:', 'opensesh' ); ?></label>
			<select id="session-type" name="session-type">
				<option value="session" <?php selected( $session_type, 'session' ); ?>><?php _e( 'Regular Session', 'opensesh' ); ?></option>
				<option value="custom" <?php selected( $session_type, 'custom' ); ?>><?php _e( 'Break, Lunch, etc.', 'opensesh' ); ?></option>
			</select>
		</p>

	<?php
	}

	/**
	 * Fired when a post is saved, updates additional sessions metadada.
	 */
	public function save_meta_box( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || $post->post_type != 'session' ) {
			return;
		}

		if ( isset( $_POST['meta-speakers-list-nonce'] ) && wp_verify_nonce( $_POST['meta-speakers-list-nonce'], 'edit-speakers-list' ) && current_user_can( 'edit_post', $post_id ) ) {

			// Update the text box as is for backwards compatibility.
			$speakers = sanitize_text_field( $_POST['speakers-list'] );
			update_post_meta( $post_id, '_session_speakers', $speakers );
		}

		// Update session time.
		if ( isset( $_POST['meta-session-info'] ) && wp_verify_nonce( $_POST['meta-session-info'], 'edit-session-info' ) ) {
			$session_time = sanitize_text_field( $_POST['session-time'] );
			$session_time = strtotime( $session_time );
			update_post_meta( $post_id, '_session_time', $session_time );

			$session_type = sanitize_text_field( $_POST['session-type'] );

			if ( ! in_array( $session_type, array( 'session', 'custom' ) ) ) {
				$session_type = 'session';
			}

			update_post_meta( $post_id, '_session_type', $session_type );
		}

		// Allowed outside of $_POST. If anything updates a session, make sure
		// we parse the list of speakers and add the references to speakers.
		$speakers_list = get_post_meta( $post_id, '_session_speakers', true );
		$speakers_list = explode( ',', $speakers_list );

		if ( ! is_array( $speakers_list ) ) {
			$speakers_list = array();
		}

		$speaker_ids = array();
		$speakers    = array_unique( array_map( 'trim', $speakers_list ) );
		foreach ( $speakers as $speaker_name ) {
			if ( empty( $speaker_name ) ) {
				continue;
			}

			// Look for speakers by their names.
			$speaker = get_page_by_title( $speaker_name, OBJECT, 'speaker' );

			if ( $speaker ) {
				$speaker_ids[] = $speaker->ID;
			}
		}

		// Add speaker IDs to post meta.
		$speaker_ids = array_unique( $speaker_ids );
		delete_post_meta( $post_id, '_speaker_id' );

		foreach ( $speaker_ids as $speaker_id ) {
			add_post_meta( $post_id, '_speaker_id', $speaker_id );
		}
	}

}