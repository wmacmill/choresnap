<?php
/**
 * WooCommerce - Bookings 
 */

class Listify_WooCommerce_Bookings extends Listify_Integration {

	public function __construct() {
		if ( ! class_exists( 'WP_Job_Manager_Products' ) ) {
			return;
		}

		$this->integration = 'woocommerce-bookings';

		$this->includes = array(

		);

		parent::__construct();
	}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		$wpjmp = WPJMP();

		remove_action( 'single_job_listing_end', array( $wpjmp->products, 'listing_display_products' ) );
	}

	public function widgets_init() {
		$widgets = array(
			'job_listing-bookings.php'
		);

		foreach ( $widgets as $widget ) {
			include_once( listify_Integration::get_dir() . 'widgets/class-widget-' . $widget );
		}

		register_widget( 'Listify_Widget_Listing_Bookings' );
	}

	public function get_bookable_products( $post_id ) {
		$products = get_post_meta( $post_id, '_products', true );
		
		if ( ! $products ) {
			return;
		}

		$_products = array();

		foreach ( $products as $product ) {
			$product = get_product( $product );

			if ( ! $product ) {
				continue;
			}

			if ( 'booking' != $product->product_type ) {
				continue;
			}

			$_products[] = $product;
		}

		if ( empty( $_products ) ) {
			return false;
		}

		return $_products;
	}
}

$GLOBALS[ 'listify_woocommerce_bookings' ] = new Listify_WooCommerce_Bookings();
