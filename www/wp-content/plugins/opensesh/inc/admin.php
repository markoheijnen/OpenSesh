<?php
class OpenSesh_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'load_menu' ) );

	}

	public function load_menu() {
		add_menu_page(
			__( 'Control Center', 'opensesh' ),
			__( 'Control Center', 'opensesh' ),
			'manage_options',
			'opensesh',
			array( $this, 'control_center' ),
			'dashicons-video-alt',
			3
		);

		add_submenu_page(
			'opensesh',
			__( 'Information', 'opensesh' ),
			__( 'Information', 'opensesh' ),
			'manage_options',
			'opensesh-info',
			array( $this, 'information_page' )
		);
	}

	public function information_page() {

		echo '<div class="wrap">';
		echo '<h2>' . get_admin_page_title() . '</h2>';

		echo '</div>';

	}

	public function control_center() {
		$keyword = '';
		$movies  = array( 'items' => array() );

		if ( isset( $_POST['youtube-s'] ) ) {
			$keyword = sanitize_text_field( $_POST['youtube-s'] );

			$movies = OpenSesh_Youtube::search( $keyword );
		}
		else if( isset( $_GET['play'] ) ) {
			//OpenSesh_Nodejs::publish( 1, 'http://markoheijnen.com' );
		}

		echo '<div class="wrap">';
		echo '<h2>' . get_admin_page_title() . '</h2>';

		if ( ! OpenSesh_Nodejs::status() ) {
			echo '<div class="error settings-error nodejs-down"><p>';
			echo '<strong>' . __( 'Currently our front-end server is down.', 'opensesh' ) . '</strong>';
			echo '</p></div>';
		}


		echo '<h3>' . __( 'Control your channels', 'opensesh' ) . '</h3>';
		echo '<form action="" method="post" class="channels">';

		$channels = OpenSesh_Nodejs::channels();
		for ( $i = 1; $i <= OpenSesh::$amount_channels; $i++ ) {
			$src = '';

			if ( isset( $channels[ $i ]->url ) ) {
				$src = $channels[ $i ]->url;
			}

			echo '<div id="channel-' . $i . '" class="channel">';
			echo '<h4>' . sprintf( __( 'Channel: %s', 'opensesh' ), $i ) . '</h4>';
			echo '<div class="channel-movie"><iframe width="100%" height="315" src="' . $src . '" frameborder="0" allowfullscreen></iframe></div>';
			echo '<div class="channel-input">';
			echo '<input type="text" class="channel-link" placeholder="' . __( 'Link to YouTube video', 'opensesh' ) . '" name="channel[' . $i . '][link]" value="" />';
			echo '<input type="button" class="channel-submit button button-primary" data-channel="' . $i . '"" name="channel[' . $i . '][link]" value="' . __( 'Send', 'opensesh' ) . '" />';
			echo '</div>';
			echo '</div>';
		}
		echo '</form>';



		echo '<h3>' . __( 'Search for a YouTube Movie', 'opensesh' ) . '</h3>';
		echo '<form action="" method="post" class="youtube-search">';
		echo '<div class="youtube-search-input"><input type="text" name="youtube-s" value="' . $keyword . '" placeholder="' . __( 'Find a YouTube movie to display.', 'opensesh' ) . '"></div>';
		echo '<button type="submit" class="button-primary">' . __( 'Search', 'opensesh' ) . '</button>';
		echo '</form>';

		echo '<div class="theme-browser rendered">';
		echo '<div class="themes">';

		foreach ( $movies['items'] as $movie ) {
			?>
			<div class="theme" data-id="<?php echo $movie['id']; ?>" data-mobile="<?php echo $movie['mobile']; ?>">
				<div class="theme-screenshot">
					<img src="<?php echo $movie['thumbnail']['high']; ?>" alt="">
				</div>

				<span class="more-details"><?php echo $movie['title']; ?></span>

				<h3 class="theme-name"><?php echo $movie['title']; ?></h3>

				<div class="theme-actions">
					<?php if ( OpenSesh_Nodejs::status() ) { ?>
						<a class="button button-primary play" href="<?php echo admin_url('admin.php?page=opensesh&play=' . $movie['id'] . '&channels=1,2'); ?>" data-channels="1,2"><?php _e( 'All', 'opensesh' ); ?></a>
						<a class="button button-primary play" href="<?php echo admin_url('admin.php?page=opensesh&play=' . $movie['id'] . '&channels=1'); ?>" data-channels="1"><?php _e( 'Channel 1', 'opensesh' ); ?></a>
						<a class="button button-primary play" href="<?php echo admin_url('admin.php?page=opensesh&play=' . $movie['id'] . '&channels=2'); ?>" data-channels="2"><?php _e( 'Channel 2', 'opensesh' ); ?></a>
					<?php } ?>
				</div>

			</div>

		<?php
		}


		echo '</div>';
		echo '<br class="clear">';
		echo '</div>';


		wp_enqueue_style( 'opensesh-admin' );
		wp_enqueue_script( 'opensesh-admin' );
	}

}