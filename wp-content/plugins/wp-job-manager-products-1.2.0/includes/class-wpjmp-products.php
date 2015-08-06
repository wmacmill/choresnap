<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMP_Products.
 *
 *	This class handles everything concerning the products.
 *
 *	@class		WPJMP_Products
 *	@version	1.0.0
 *	@author		Jeroen Sormani
 */
class WPJMP_Products {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Add products field to submit form
		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );

		// Save products from submit form
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data_products' ), 10, 2 );

		// Add products field to backend
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'add_listing_data_fields_product' ) );

		// Add multiselect chosen to backend
		add_action( 'job_manager_input_multi_chosen', array( $this, 'input_multi_chosen' ), 10, 2 );

		// Display products on listing page
		add_action( 'single_job_listing_end', array( $this, 'listing_display_products' ) );

		// Add little-bit of inline style
		add_action( 'job_manager_job_listing_data_start', array( $this, 'job_listing_data_start_style' ) );

		// Save an empty value when no products are in $_POST
		add_action( 'job_manager_save_job_listing', array( $this, 'save_job_listing_data' ), 25, 2 );

	}


	/**
	 * Product field.
	 *
	 * Add a select product field to the submit listing form products.
	 * (front-end).
	 *
	 * @since 1.0.0
	 *
	 * @param 	array $fields 	List of settingsfields.
	 * @return	array			Modified list of settingsfields.
	 */
	public function submit_job_form_fields( $fields ) {

		$options 		= array();
		$product_args 	= array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> '-1',
			'meta_query' 		=> array(
				array(
					'key'		=> '_visibility',
					'value'		=> 'hidden',
					'compare'	=> '!=',
				),
			),
		);
		if ( 'own' == get_option( 'wpjmp_products_limit', 'own' ) ) :
			// Don't show this field when user is not logged in
			if ( ! is_user_logged_in() ) :
				return $fields;
			endif;

			$product_args['author'] = get_current_user_id();
		endif;

		$products = get_posts( apply_filters( 'wpjmp_job_form_products_args', $product_args ) );

		foreach ( $products as $product ) :
			$options[ $product->ID ] = $product->post_title;
		endforeach;

	    if ( empty( $options ) ) :
	    	return $fields;
	    endif;

		$fields['company']['products'] = array(
			'label'			=> get_option( 'wpjmp_select_products_text' ),
			'type'			=> 'multiselect',
			'options'		=> $options,
			'required'		=> false,
			'priority' 		=> 10,
		);

		return $fields;

	}


	/**
	 * Save submit.
	 *
	 * Save the products when a listing is submitted.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int/numberic $job_id List of settingsfields.
	 * @param 	array		 $values List of posted values.
	 */
	public function update_job_data_products( $job_id, $values ) {

		update_post_meta( $job_id, '_products', $values['company']['products'] );

	}


	/**
	 * Style.
	 *
	 * Add a little bit of inline styling to the meta box.
	 *
	 * @since 1.0.0
	 */
	public function job_listing_data_start_style() {

		?><style>
			.wp_job_manager_meta_data p {
				overflow: visible !important;
			}
			.wp_job_manager_meta_data p div.chosen-container {
				min-width: 50% !important;
			}
		</style><?php

	}


	/**
	 * Product field.
	 *
	 * Add a product field to the admin area with the chosen products.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array $fields 	List of settingsfields.
	 * @return	array			Modified list of settingsfields.
	 */
	public function add_listing_data_fields_product( $fields ) {

		$user_id 		= get_current_user_id();
		$product_args 	= array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> '-1',
		);
		if ( 'own' == get_option( 'wpjmp_products_limit', 'own' ) ) :
			$product_args['author'] = get_current_user_id();
		endif;

		$products = get_posts( $product_args );

		foreach ( $products as $product ) :
			$options[ $product->ID ] = $product->post_title;
		endforeach;

		if ( empty( $options ) ) :
			return $fields;
		endif;

		$fields['_products'] = array(
			'label' 		=> get_option( 'wpjmp_select_products_text' ),
			'placeholder'	=> '',
			'type'			=> 'multiselect',
			'options'		=> $options,
		);

		return $fields;

	}


	/**
	 * Save products.
	 *
	 * Update the meta when its empty (not done by WP JM by default.
	 * (admin)
	 *
	 * @since 1.0.0
	 */
	public function save_job_listing_data( $post_id, $post ) {

		if ( ! isset( $_POST['_products'] ) ) :
			update_post_meta( $post_id, '_products', '' );
		endif;

	}


	/**
	 * Listing products.
	 *
	 * Display the chosen products on the listing page.
	 * Uses the default WC template to display the products.
	 *
	 * @since 1.0.0
	 */
	public function listing_display_products() {

		global $post;

		$products = get_post_meta( $post->ID, '_products', true );

		// Stop if there are no products
		if ( ! $products || ! is_array( $products ) ) :
			return;
		endif;

		$args = apply_filters( 'woocommerce_related_products_args', array(
			'post_type'            => 'product',
			'ignore_sticky_posts'  => 1,
			'no_found_rows'        => 1,
			'posts_per_page'       => -1,
			'post__in'             => $products,
		) );

		$products = new WP_Query( $args );

		if ( $products->have_posts() ) : ?>

			<div class="listing products woocommerce">

				<h2><?php echo get_option( 'wpjmp_listing_products_text' ); ?></h2>

				<?php woocommerce_product_loop_start();

					while ( $products->have_posts() ) : $products->the_post();

						wc_get_template_part( 'content', 'product' );

					endwhile;

				woocommerce_product_loop_end(); ?>

			</div>

		<?php endif;

		wp_reset_postdata();

	}


}
