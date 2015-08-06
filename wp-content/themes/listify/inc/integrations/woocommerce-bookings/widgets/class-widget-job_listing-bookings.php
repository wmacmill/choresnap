<?php
/**
 * Job Listing: Bookings
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Bookings extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the booking form for the linked bookable product.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_bookings';
		$this->widget_name        = __( 'Listify - Listing: Bookings', 'listify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'listify' )
			),
			'icon' => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Icon:', 'listify' ),
				'options' => $this->get_icon_list()
			)
		);
		
		parent::__construct();

		if ( is_active_widget( false, false, $this->widget_id, true ) ) {
			add_action( 'listify_single_job_listing_actions_after', array( $this, 'output_button' ) );
		}
	}

	public function output_button() {
		global $post, $listify_woocommerce_bookings;

		$products = $listify_woocommerce_bookings->get_bookable_products( $post->ID );
	
		if ( ! $products ) {
			return;
		}

		echo '<a href="#wc-bookings-booking-form" class="button">' . __( 'Book Now', 'listify' ) . '</a>';
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		ob_start();

		global $post, $product, $listify_woocommerce_bookings;		

		$products = $listify_woocommerce_bookings->get_bookable_products( $post->ID );
		
		if ( ! $products ) {
			return;
		}

		$wpjmp = WPJMP();

		remove_action( 'single_job_listing_end', array( $wpjmp->products, 'listing_display_products' ) );

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

        do_action( 'listify_widget_job_listing_bookings_before' );

		foreach ( $products as $product ) {
			// Prepare form
			$booking_form = new WC_Booking_Form( $product );

            echo '<h3 class="booking-form-title">' . $product->post->post_title . ':</h3>'; 

			// Get template
			woocommerce_get_template( 'single-product/add-to-cart/booking.php', array( 'booking_form' => $booking_form ), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
		}

        do_action( 'listify_widget_job_listing_bookings_after' );

		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
