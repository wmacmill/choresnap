<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Orders
 */
class WC_Paid_Listings_Orders {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_thankyou', array( $this, 'woocommerce_thankyou' ), 5 );

		// Displaying user packages on the frontend
		add_action( 'woocommerce_before_my_account', array( $this, 'my_packages' ) );

		// Statuses
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ) );

		// User deletion
		add_action( 'delete_user', array( $this, 'delete_user_packages' ) );
	}

	/**
	 * Thanks page
	 */
	public function woocommerce_thankyou( $order_id ) {
		global $wp_post_types;

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item ) {
			if ( isset( $item['job_id'] ) && 'publish' === get_post_status( $item['job_id'] ) ) {
				switch ( get_post_status( $item['job_id'] ) ) {
					case 'pending' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once approved.', 'wp-job-manager-wc-paid-listings' ), get_the_title( $item['job_id'] ) ) );
					break;
					case 'pending_payment' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once payment has been confirmed.', 'wp-job-manager-wc-paid-listings' ), get_the_title( $item['job_id'] ) ) );
					break;
					default :
						echo wpautop( sprintf( __( '%s has been submitted successfully.', 'wp-job-manager-wc-paid-listings' ), get_the_title( $item['job_id'] ) ) );
					break;
				}

				echo '<p class="job-manager-submitted-paid-listing-actions">';

				if ( 'publish' === get_post_status( $item['job_id'] ) ) {
					echo '<a class="button" href="' . get_permalink( $item['job_id'] ) . '">' . __( 'View Listing', 'wp-job-manager-wc-paid-listings' ) . '</a> ';
				} elseif ( get_option( 'job_manager_job_dashboard_page_id' ) ) {
					echo '<a class="button" href="' . get_permalink( get_option( 'job_manager_job_dashboard_page_id' ) ) . '">' . __( 'View Dashboard', 'wp-job-manager-wc-paid-listings' ) . '</a> ';
				}

				echo '</p>';

			} elseif ( isset( $item['resume_id'] ) ) {
				$resume = get_post( $item['resume_id'] );

				switch ( get_post_status( $item['resume_id'] ) ) {
					case 'pending' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once approved.', 'wp-job-manager-wc-paid-listings' ), get_the_title( $item['resume_id'] ) ) );
					break;
					case 'pending_payment' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once payment has been confirmed.', 'wp-job-manager-wc-paid-listings' ), get_the_title( $item['resume_id'] ) ) );
					break;
					default :
						echo wpautop( sprintf( __( '%s has been submitted successfully.', 'wp-job-manager-wc-paid-listings' ), get_the_title( $item['resume_id'] ) ) );
					break;
				}

				echo '<p class="job-manager-submitted-paid-listing-actions">';

				if ( 'publish' === get_post_status( $item['resume_id'] ) ) {
					echo '<a class="button" href="' . get_permalink( $item['resume_id'] ) . '">' . __( 'View Listing', 'wp-job-manager-wc-paid-listings' ) . '</a> ';
				} elseif ( get_option( 'resume_manager_candidate_dashboard_page_id' ) ) {
					echo '<a class="button" href="' . get_permalink( get_option( 'resume_manager_candidate_dashboard_page_id' ) ) . '">' . __( 'View Dashboard', 'wp-job-manager-wc-paid-listings' ) . '</a> ';
				}

				if ( ! empty( $resume->_applying_for_job_id ) ) {
					echo '<a class="button" href="' . get_permalink( absint( $resume->_applying_for_job_id ) ) . '">' . sprintf( __( 'Apply for "%s"', 'wp-job-manager-wc-paid-listings' ), get_the_title( absint( $resume->_applying_for_job_id ) ) ) . '</a> ';
				}

				echo '</p>';
			}
		}
	}

	/**
	 * Show my packages
	 */
	public function my_packages() {
		if ( ( $packages = wc_paid_listings_get_user_packages( get_current_user_id(), 'job_listing' ) ) && is_array( $packages ) && sizeof( $packages ) > 0 ) {
			woocommerce_get_template( 'my-packages.php', array( 'packages' => $packages, 'type' => 'job_listing' ), 'wc-paid-listings/', JOB_MANAGER_WCPL_TEMPLATE_PATH );
		}
		if ( ( $packages = wc_paid_listings_get_user_packages( get_current_user_id(), 'resume' ) ) && is_array( $packages ) && sizeof( $packages ) > 0 ) {
			woocommerce_get_template( 'my-packages.php', array( 'packages' => $packages, 'type' => 'resume' ), 'wc-paid-listings/', JOB_MANAGER_WCPL_TEMPLATE_PATH );
		}
	}

	/**
	 * Triggered when an order is paid
	 * @param  int $order_id
	 */
	public function order_paid( $order_id ) {
		// Get the order
		$order = wc_get_order( $order_id );

		if ( get_post_meta( $order_id, 'wc_paid_listings_packages_processed', true ) ) {
			return;
		}
		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );

			if ( $product->is_type( array( 'job_package', 'resume_package' ) ) && $order->customer_user ) {

				// Give packages to user
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = wc_paid_listings_give_user_package( $order->customer_user, $product->id, $order_id );
				}

				// Approve job or resume with new package
				if ( isset( $item['job_id'] ) ) {
					$job = get_post( $item['job_id'] );

					if ( in_array( $job->post_status, array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_job_listing_with_package( $job->ID, $order->customer_user, $user_package_id );
					}
				} elseif( isset( $item['resume_id'] ) ) {
					$resume = get_post( $item['resume_id'] );

					if ( in_array( $resume->post_status, array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_resume_with_package( $resume->ID, $order->customer_user, $user_package_id );
					}
				}
			}
		}

		update_post_meta( $order_id, 'wc_paid_listings_packages_processed', true );
	}

	/**
	 * Delete packages on user deletion
	 */
	public function delete_user_packages( $user_id ) {
		global $wpdb;

		if ( $user_id ) {
			$wpdb->delete(
				"{$wpdb->prefix}wcpl_user_packages",
				array(
					'user_id' => $user_id
				)
			);
		}
	}
}
WC_Paid_Listings_Orders::get_instance();
