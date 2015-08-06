<?php

class Listify_Page_Settings {

	public function __construct() {
		add_action( 'init', array( $this, 'register_meta' ) );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	public function register_meta() {
		register_meta( 'post', 'enable_tertiary_navigation', 'absint' );
		register_meta( 'post', 'hero_style', 'esc_attr' );
		register_meta( 'post', 'video_url', 'esc_url' );
	}

	public function add_meta_box() {
		add_meta_box( 'listify-settings', __( 'Page Settings', 'listify' ), array( $this, 'meta_box_settings' ), 'page', 'side' );
	}

	public function meta_box_settings() {
		global $post;

		$tertiary  = $post->enable_tertiary_navigation;
		$hero      = $post->hero_style ? $post->hero_style : 'image';
		$video_url = $post->video_url;
	?>

		<p>
			<label for="enable_tertiary_navigation">
				<input type="checkbox" name="enable_tertiary_navigation" id="enable_tertiary_navigation" value="1" <?php checked(1, $tertiary); ?>>
				<?php _e( 'Show tertiary navigation bar', 'listify' ); ?>
			</label>
		</p>

		<p><strong><?php _e( 'Hero Style', 'listify' ); ?></strong></p>

		<p>
			<label for="hero-style-none">
				<input type="radio" name="hero_style" id="hero-style-none" value="none" <?php checked('none', $hero); ?>>
				<?php _e( 'None', 'listify' ); ?>
			</label><br />

			<label for="hero-style-image">
				<input type="radio" name="hero_style" id="hero-style-image" value="image" <?php checked('image', $hero); ?>>
				<?php _e( 'Featured Image', 'listify' ); ?>
			</label><br />

			<label for="hero-style-video">
				<input type="radio" name="hero_style" id="hero-style-video" value="video" <?php checked('video', $hero); ?>>
				<?php _e( 'Video', 'listify' ); ?>
			</label><br />

			<label for="hero-style-map">
				<input type="radio" name="hero_style" id="hero-style-map" value="map" <?php checked('map', $hero); ?>>
				<?php _e( 'Map', 'listify' ); ?>
			</label>
		</p>

	<?php
	}

	public function save_post( $post_id ) {
		global $post;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'page' != $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		$tertiary = isset( $_POST[ 'enable_tertiary_navigation' ] ) ? 1 : 0;
		$hero = isset( $_POST[ 'hero_style' ] ) ? $_POST[ 'hero_style' ] : '';
		;

		update_post_meta( $post->ID, 'enable_tertiary_navigation', $tertiary );
		update_post_meta( $post->ID, 'hero_style', $hero );
	}

}

$GLOBALS[ 'classiy_page_settings' ] = new Listify_Page_Settings();
