<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMP_Settings.
 *
 *	This class handles everything concerning the settings.
 *
 *	@class		WPJMP_Settings
 *	@version	1.0.0
 *	@author		Jeroen Sormani
 */
class WPJMP_Settings {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Settings tab
		add_action( 'job_manager_settings', array( $this, 'wpjmp_settings' ) );

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
	public function wpjmp_settings( $settings )  {

		$settings['wpjmp_settings'] = array(
			__( 'Products', 'wp-job-manager-products' ),
			array(

				array(
					'name'			=> 'wpjmp_products_limit',
					'type'			=> 'select',
					'label'			=> __( 'Products limitation', 'wp-job-manager-products' ),
					'desc'			=> __( 'What products can listing owners select?', 'wp-job-manager-products' ),
					'options'		=> array(
						'own' => __( 'Only their own', 'wp-job-manager-products' ),
						'all' => __( 'All', 'wp-job-manager-products' ),
					),
				),

				array(
					'name'			=> 'wpjmp_select_products_text',
					'type'			=> 'text',
					'label'			=> __( 'Select products text', 'wp-job-manager-products' ),
					'desc'			=> __( '<em>default: "Select Your Services & Products"</em>', 'wp-job-manager-products' ),
					'std'			=> 'Select Your Services & Products',
				),

				array(
					'name'			=> 'wpjmp_listing_products_text',
					'type'			=> 'text',
					'label'			=> __( 'Listing products text', 'wp-job-manager-products' ),
					'desc'			=> __( '<em>default: "Listing products"</em>', 'wp-job-manager-products' ),
					'std'			=> __( 'Listing products', 'wp-job-manager-products' ),
				),

			),
		);

		return $settings;

	}

}
