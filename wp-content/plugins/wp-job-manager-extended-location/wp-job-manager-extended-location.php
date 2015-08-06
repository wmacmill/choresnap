<?php
/*
Plugin Name: WP Job Manager - Extended Location
Plugin URI: https://astoundify.com/downloads/wp-job-manager-google-places-suggest/
Description: Use Google Places to auto suggest locations when submitting a listing or searching.
Version: 2.2.0
Author: Astoundify
Author URI: http://astoundify.com
Text Domain: wp-job-manager-extended-location
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WP_Job_Manager_Extended_Location
 *
 *	Main WPJMEL class initializes the plugin
 *
 *	@class		WP_Job_Manager_Extended_Location
 *	@version	1.0.0
 *	@author		Jeroen Sormani
 */
class WP_Job_Manager_Extended_Location {


	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '2.1.0';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Instace of WP_Job_Manager_Extended_Location.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of WPJMEL.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'updater' ), 9 );

		// Save Map geo location
		add_action( 'job_manager_update_job_data', array( $this, 'save_submit_listing_geo' ), 25, 2 );

		/**
		 * Settings class
		 */
		if ( is_admin() ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-wpjmel-settings.php';
			$this->settings = new WPJMEL_Settings();
		}
	}

	/**
	 * Updater.
	 *
	 * Include the updater script.
	 */
	public function updater() {
		include_once( 'includes/updater/class-astoundify-updater.php' );

		new Astoundify_Updater_Extended_Location( __FILE__ );
	}

	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue javascripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
        if ( ! wp_script_is( 'google-maps', 'registered' ) ) {
            wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false' );
        }

        wp_enqueue_style( 'wpjm-extended-location', plugins_url( '/assets/css/wp-job-manager-extended-location.css', __FILE__ ) );

		wp_register_script( 'wpjm-extended-location', plugins_url( '/assets/js/wp-job-manager-extended-location.js', __FILE__ ), array( 'jquery', 'google-maps' ), $this->version, true );

		$args = array(
			'start_point'		=> apply_filters( 'wpjmel_map_start_point', get_option( 'wpjmel_map_start_location' ) ),
			'start_geo_lat'		=> apply_filters( 'wpjmgel_map_start_geo_lat', get_option( 'wpjmel_start_geo_lat' ) ),
			'start_geo_long'	=> apply_filters( 'wpjmgel_map_start_geo_long', get_option( 'wpjmel_start_geo_long' ) ),
			'enable_map' 		=> get_option( 'wpjmel_enable_map', 1 ),
			'map_elements' 		=> apply_filters( 'wpjmel_map_elements', '#job_location, #setting-wpjmel_map_start_location' ),
			'user_location'		=> $this->get_user_ip_location(),
			'file_path'			=> plugins_url( './assets', __FILE__ ),
		);

		if ( isset ( $_REQUEST[ 'job_id' ] ) ) {
			$job = absint( $_REQUEST[ 'job_id' ] );

			$args[ 'list_geo_lat' ] = get_post_meta( $job, 'geolocation_lat', true );
			$args[ 'list_geo_long' ] = get_post_meta( $job, 'geolocation_long', true );
		} else {
			$args[ 'list_geo_lat' ]	= isset( $_POST[ 'geo_lat' ] ) ? esc_attr( $_POST[ 'geo_lat' ] ) : '';
			$args[ 'list_geo_long' ] = isset( $_POST[ 'geo_lng' ] ) ? esc_attr( $_POST[ 'geo_lng' ] ) : '';
		}

		wp_localize_script( 'wpjm-extended-location', 'wpjmel', $args );
		wp_enqueue_script( 'wpjm-extended-location' );

		wp_enqueue_script( 'geo-tag-text', plugins_url( '/assets/js/geo-tag-text.js', __FILE__ ), array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'mapify', plugins_url( '/assets/js/mapify.js', __FILE__ ), array( 'jquery' ), $this->version, false );
	}

	/**
	 * Admin scripts.
	 *
	 * Enqueue admin javascripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false' );

        wp_enqueue_style( 'wpjm-extended-location', plugins_url( '/assets/css/wp-job-manager-extended-location.css', __FILE__ ) );
		wp_register_script( 'wpjm-extended-location', plugins_url( '/assets/js/wp-job-manager-extended-location-admin.js', __FILE__ ), array( 'jquery', 'geo-tag-text', 'mapify' ), $this->version, true );

		wp_localize_script( 'wpjm-extended-location', 'wpjmel', array(
			'start_point'		=> apply_filters( 'wpjmgel_map_start_point', get_option( 'wpjmel_map_start_location' ) ),
			'start_geo_lat'		=> apply_filters( 'wpjmgel_map_start_geo_lat', get_option( 'wpjmel_start_geo_lat', 40.712784 ) ),
			'start_geo_long'	=> apply_filters( 'wpjmgel_map_start_geo_long', get_option( 'wpjmel_start_geo_long', -74.005941 ) ),
			'enable_map' 		=> get_option( 'wpjmgel_enable_map', 1 ),
			'user_location'		=> $this->get_user_ip_location(),
			'listing_lat'		=> isset( $_GET['post'] ) ? get_post_meta( $_GET['post'], 'geolocation_lat', true ) : null,
			'listing_long'		=> isset( $_GET['post'] ) ? get_post_meta( $_GET['post'], 'geolocation_long', true ) : null,
		) );
		wp_enqueue_script( 'wpjm-extended-location' );

		wp_enqueue_script( 'geo-tag-text', plugins_url( '/assets/js/geo-tag-text.js', __FILE__ ), array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'mapify', plugins_url( '/assets/js/mapify.js', __FILE__ ), array( 'jquery' ), $this->version, false );
	}

	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.0.1
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-job-manager-extended-location', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * IP location.
	 *
	 * Get the users location information by IP.
	 *
	 * @since 1.0.1
	 */
	public function get_user_ip_location() {
		// Stop if location suggest is disabled
		if ( 0 == get_option( 'wpjmel_enable_city_suggest' ) ) {
			return array();
		}

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}

		$hash = 'wpjmel_location_' . md5( $ip );
		if ( ! $location_data = get_transient( $hash ) ) {
			$location_data = json_decode( file_get_contents( 'http://www.telize.com/geoip/' . $ip ) );
			set_transient( $hash, $location_data, 60 * 60 * 24 );
		}

		return $location_data;
	}


	/**
	 * Submit listing geo.
	 *
	 * Save the GEO location on listing submit.
	 *
	 * @since 2.0.0
	 */
	public function save_submit_listing_geo( $listing_id, $values ) {

		if ( isset( $_POST['geo_lat'] ) ) :
			update_post_meta( $listing_id, 'geolocation_lat', sanitize_text_field( $_POST['geo_lat'] ) );
		endif;

		if ( isset( $_POST['geo_lng'] ) ) :
			update_post_meta( $listing_id, 'geolocation_long', sanitize_text_field( $_POST['geo_lng'] ) );
		endif;

	}

}

/**
 * The main function responsible for returning the WP_Job_Manager_Extended_Location object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php WPJMEL()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object WP_Job_Manager_Extended_Location class object.
 */
function WP_Job_Manager_Extended_Location() {
	return WP_Job_Manager_Extended_Location::instance();
}
add_action( 'plugins_loaded', 'WP_Job_Manager_Extended_Location' );
