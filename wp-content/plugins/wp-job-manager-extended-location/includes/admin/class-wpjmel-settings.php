<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMEL_Settings.
 *
 *	This class handles everything concerning the settings.
 *
 *	@class		WPJMEL_Settings
 *	@version	1.0.0
 *	@author		Astoundify
 */
class WPJMEL_Settings {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Settings tab
		add_action( 'job_manager_settings', array( $this, 'settings' ) );

		// Register start location geo settings
		add_action( 'admin_init', array( $this, 'register_geo_settings' ) );

		// Save the new GEO location
		add_action( 'job_manager_save_job_listing', array( $this, 'save_listing_geo' ), 30, 2 );

	}

	/**
	 * Start GEO location.
	 *
	 * Register settings for the start point GEO location data.
	 *
	 * @since 2.0.0
	 */
	public function register_geo_settings() {
		register_setting( 'job_manager', 'wpjmel_start_geo_lat' );
		register_setting( 'job_manager', 'wpjmel_start_geo_long' );
	}

	/**
	 * Settings page.
	 *
	 * Add an settings tab to the Listings -> settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array 	$settings	Array of default settings.
	 * @return 	array	$settings	Array including the new settings.
	 */
	public function settings( $settings )  {
		$settings['wpjmel_settings'] = array(
			__( 'Location Settings', 'wp-job-manager-extended-location' ),
			array(
				array(
					'name'			=> 'wpjmel_enable_city_suggest',
					'type'			=> 'checkbox',
					'label'			=> __( 'Auto Location', 'wp-job-manager-extended-location' ),
					'cb_label'		=> __( 'Enable City Suggest', 'wp-job-manager-extended-location' ),
					'desc'			=> __( 'When checked the city of the user will be automatically used to filter results.', 'wp-job-manager-extended-location' ),
					'std'			=> 1,
				),
				array(
					'name'			=> 'wpjmel_enable_map',
					'type'			=> 'checkbox',
					'label'			=> __( 'Submission Form', 'wp-job-manager-extended-location' ),
					'cb_label'		=> __( 'Display Map', 'wp-job-manager-extended-location' ),
					'desc'			=> __( 'When checked there will be a small Google Map positioned beneath the location field.', 'wp-job-manager-extended-location' ),
					'std'			=> 1,
				),

				array(
					'name'			=> 'wpjmel_map_start_location',
					'type'			=> 'text',
					'label'			=> __( 'Default Location', 'wp-job-manager-extended-location' ),
					'desc'			=> __( 'The start location if the map is enabled', 'wp-job-manager-extended-location' ),
					'std'			=> '',
				)
			),
		);

		return $settings;
	}

	/**
	 * Save geo.
	 *
	 * Save the GEO location when a listing is being saved
	 *
	 * @since 2.0.0
	 *
	 * @param int 		$post_id ID of the post being saved.
	 * @param object 	$post	 WP_Post object.
	 */
	public function save_listing_geo( $post_id, $posted ) {

		if ( isset( $_POST['geo_lat'] ) ) :
			update_post_meta( $post_id, 'geolocation_lat', sanitize_text_field( $_POST['geo_lat'] ) );
		endif;

		if ( isset( $_POST['geo_lng'] ) ) :
			update_post_meta( $post_id, 'geolocation_long', sanitize_text_field( $_POST['geo_lng'] ) );
		endif;

	}

}
