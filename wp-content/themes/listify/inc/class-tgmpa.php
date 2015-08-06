<?php

class Listify_TGMPA {

	public function __construct() {
		if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
			require_once( get_template_directory() . '/inc/class-tgm-plugin-activation.php' );
		}

		add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );
	}

	public function tgmpa_register() {
		$plugins = array(
			array(
				'name'      => 'WP Job Manager',
				'slug'      => 'wp-job-manager',
				'required'  => true
			),
			array(
				'name'      => 'WooCommerce',
				'slug'      => 'woocommerce',
				'required'  => true
			),
			array( 
				'name'      => 'Envato WordPress Toolkit',
				'slug'      => 'envato-wordpress-toolkit',
				'source'    => 'https://github.com/envato/envato-wordpress-toolkit/archive/master.zip',
				'external_url' => 'https://github.com/envato/envato-wordpress-toolkit',
				'required'  => false
			),
			array(
				'name'      => 'WordPress Importer',
				'slug'      => 'wordpress-importer',
				'required'  => false 
			),
			array(
				'name'      => 'Widget Importer & Exporter',
				'slug'      => 'widget-importer-exporter',
				'required'  => false,
			),
			array(
				'name'      => 'Jetpack',
				'slug'      => 'jetpack',
				'required'  => false,
			),
			array(
				'name'      => 'WP Job Manager - Predefined Regions',
				'slug'      => 'wp-job-manager-locations',
				'required'  => false
			),
			array(
				'name'      => 'Nav Menu Roles',
				'slug'      => 'nav-menu-roles',
				'required'  => false,
			),
			array(
				'name'      => 'WP Job Manager - Contact Listing',
				'slug'      => 'wp-job-manager-contact-listing',
				'required'  => false
			),
			array(
				'name'      => 'Contact Form 7',
				'slug'      => 'contact-form-7',
				'required'  => false
			)

		);

		$config = array(
			'id' => 'listify',
			'has_notices' => false
		);

		tgmpa( $plugins, $config );
	}

}

$GLOBALS[ 'listify_tgmpa' ] = new Listify_TGMPA();
