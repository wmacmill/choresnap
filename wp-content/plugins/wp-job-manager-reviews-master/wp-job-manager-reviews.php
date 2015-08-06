<?php
/*
Plugin Name: WP Job Manager - Reviews
Plugin URI: https://astoundify.com/downloads/wp-job-manager-reviews/
Description: Leave reviews for listings in WP Job Manager. Define review categories and choose the number of stars available.
Version: 1.0.0
Author: Astoundify
Author URI: https://astoundify.com
Text Domain: wp-job-manager-reviews
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WP_Job_Manager_Reviews
 *
 *	Main WPJMR class initializes the plugin
 *
 *	@class		WP_Job_Manager_Reviews
 *	@version    1.0.0
 *	@author		Jeroen Sormani
 */
class WP_Job_Manager_Reviews {


	/**
	 * Instace of WP_Job_Manager_Reviews.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of WPJMR.
	 */
	private static $instance;


	/**
	 * Array of review categories.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $review_categories Review categories array.
	 */
	private $review_categories;


	/**
	 * Number of stars.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var int $count_stars Number of stars displayed.
	 */
	private $count_stars;


	/**
	 * Allow comment rating.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var BOOL $comment_rating Allows comment ratings (default: true).
	 */
	public $comment_rating = true;


	/**
	 * Review class.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Object $review Object of review class.
	 */
	public $review;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'job_manager_settings', array( $this, 'wpjmr_settings' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpjmr_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'updater' ), 9 );
	}

	public function updater() {
		include_once( 'includes/updater/class-astoundify-updater.php' );

		new Astoundify_Updater_Reviews( __FILE__ );
	}

	/**
	 * Instace.
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
	 * init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		/**
		 * Shortcodes.
		 */
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wpjmr-shortcodes.php';

		/**
		 * Review class.
		 */
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wpjmr-review.php';

		$this->review = new WPJMR_Review();

		// Set the review categories
		$this->review_categories = $this->wpjmr_get_review_categories();

		// Set star count
		$this->count_stars = $this->wpjmr_get_count_stars();
	}

	/**
	 * Review categories.
	 *
	 * The default review categories. Can be extended via the options page or filter.
	 *
	 * @since 1.0.0
	 * @return array List of review categories to display.
	 */
	public function wpjmr_get_review_categories() {

		$default = array(
			'speed' 	=> __( 'Speed', 'wp-job-manager-reviews' ),
			'quality' 	=> __( 'Quality', 'wp-job-manager-reviews' ),
			'price' 	=> __( 'Price', 'wp-job-manager-reviews' ),
		);

		$categories = get_option( 'wpjmr_categories', $default );

		if ( ! is_array( $categories ) ) {
			$categories = explode( PHP_EOL, $categories );
		}

		return apply_filters( 'wpjmr_review_categories', $categories );
	}

	/**
	 * Return stars.
	 *
	 * Return the number of stars used to display. Default is 5;
	 *
	 * @since 1.0.0
	 *
	 * @param int $stars Number of stars.
	 * @return int Number of stars.
	 */
	public function wpjmr_get_count_stars() {
		$stars = get_option( 'wpjmr_star_count', 5 );

		return apply_filters( 'wpjmr_count_stars', $stars );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue all style en javascripts.
	 *
	 * @since 1.0.0
	 */
	public function wpjmr_enqueue_scripts() {
		// General stylesheet
		wp_enqueue_style( 'wp-job-manager-reviews', plugins_url( 'assets/css/wp-job-manager-reviews.css', __FILE__ ) );

		// Enqueue dashicons for front-end display
		wp_enqueue_style( 'dashicons' );

		// Javascript
		wp_enqueue_script( 'wp-job-manager-reviews-js', plugins_url( 'assets/js/wp-job-manager-reviews.js', __FILE__ ), array( 'jquery' ) );
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
	public function wpjmr_settings( $settings )  {
		$default_categories = '';

		if ( is_array( $this->wpjmr_get_review_categories() ) ) :
			$default_categories = implode( PHP_EOL, $this->wpjmr_get_review_categories() );
		endif;

		$settings['wpjmr_settings'] = array(
			__( 'Reviews', 'wp-job-manager-reviews' ),
			array(
				array(
					'name'			=> 'wpjmr_star_count',
					'std'			=> '5',
					'placeholder'	=> '',
					'label'			=> __( 'Stars', 'wp-job-manager-reviews' ),
					'desc'			=> __( 'How many stars would you like to use?', 'wp-job-manager-reviews' ),
					'attributes'	=> array()
				),
				array(
					'name'			=> 'wpjmr_categories',
					'std'			=> $default_categories,
					'placeholder'	=> '',
					'label'			=> __( 'Review categories', 'wp-job-manager-reviews' ),
					'desc'			=> __( 'Categories you would you like to use, each category on one line.', 'wp-job-manager-reviews' ),
					'attributes'	=> array(),
					'type'			=> 'textarea'
				)
			),
		);

		return $settings;
	}

}

/**
 * The main function responsible for returning the WP_Job_Manager_Reviews object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php WPJMR()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object WP_Job_Manager_Reviews class object.
 */
function wpjmr() {
	return WP_Job_Manager_Reviews::instance();
}
add_action( 'plugins_loaded', 'wpjmr' );
