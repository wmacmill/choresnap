<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Paid_Listings_Subscriptions
 */
class WC_Paid_Listings_Subscriptions {

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
		if ( class_exists( 'WC_Subscriptions_Synchroniser' ) && method_exists( 'WC_Subscriptions_Synchroniser', 'save_subscription_meta' ) ) {
			add_action( 'woocommerce_process_product_meta_job_package_subscription', 'WC_Subscriptions_Synchroniser::save_subscription_meta', 10 );
			add_action( 'woocommerce_process_product_meta_resume_package_subscription', 'WC_Subscriptions_Synchroniser::save_subscription_meta', 10 );
		}
		add_action( 'added_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_filter( 'woocommerce_is_subscription', array( $this, 'woocommerce_is_subscription' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'wp_trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );
		add_action( 'publish_to_expired', array( $this, 'check_expired_listing' ) );

		// WC Subs 2.0 hooks
		add_action( 'woocommerce_scheduled_subscription_expiration', array( $this, 'subscription_ended' ) ); // When a subscription expires
		add_action( 'woocommerce_scheduled_subscription_end_of_prepaid_term', array( $this, 'subscription_ended' ) ); // When a subscription ends after remaining unpaid
		add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'subscription_ended' ) ); // When the subscription status changes to cancelled
		add_action( 'woocommerce_subscription_status_active', array( $this, 'subscription_activated' ) ); // When the subscription status changes to active
		add_action( 'woocommerce_subscription_renewal_payment_complete', array( $this, 'subscription_renewed' ) ); // When the subscription is renewed
		add_action( 'woocommerce_subscriptions_switched_item', array( $this, 'subscription_switched' ), 10, 3 ); // When the subscription is switched and a new subscription is created
		add_action( 'woocommerce_subscription_item_switched', array( $this, 'subscription_item_switched' ), 10, 4 ); // When the subscription is switched and only the item is changed
	}

	/**
	 * Prevent listings linked to subscriptions from expiring.
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( 'job_listing' === get_post_type( $object_id ) && '' !== $meta_value && '_job_expires' === $meta_key ) {
			$_package_id = get_post_meta( $object_id, '_package_id', true );
			$package     = wc_get_product( $_package_id );

			if ( $package && 'listing' === $package->package_subscription_type ) {
				update_post_meta( $object_id, '_job_expires', '' ); // Never expire automatically
			}
		}
	}

	/**
	 * get subscription type for pacakge by ID
	 * @param  int $product_id
	 * @return string
	 */
	public function get_package_subscription_type( $product_id ) {
		$subscription_type = get_post_meta( $product_id, '_package_subscription_type', true );
		return empty( $subscription_type ) ? 'package' : $subscription_type;
	}

	/**
	 * Is this a subscription product?
	 * @return bool
	 */
	public function woocommerce_is_subscription( $is_subscription, $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) ) {
			$is_subscription = true;
		}
		return $is_subscription;
	}

	/**
	 * If a listing is expired, the pack may need it's listing count changing
	 */
	public function check_expired_listing( $post ) {
		global $wpdb;

		if ( 'job_listing' === $post->post_type || 'resume' === $post->post_type ) {
			$package_product_id = get_post_meta( $post->ID, '_package_id', true );
			$package_id         = get_post_meta( $post->ID, '_user_package_id', true );
			$package_product    = get_post( $package_product_id );

			if ( $package_product_id ) {
				$subscription_type = $this->get_package_subscription_type( $package_product_id );

				if ( 'listing' === $subscription_type ) {
					$new_count = $wpdb->get_var( $wpdb->prepare( "SELECT package_count FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
					$new_count --;

					$wpdb->update(
						"{$wpdb->prefix}wcpl_user_packages",
						array(
							'package_count'  => max( 0, $new_count )
						),
						array(
							'id' => $package_id
						)
					);

					// Remove package meta after adjustment
					delete_post_meta( $post->ID, '_package_id' );
					delete_post_meta( $post->ID, '_user_package_id' );
				}
			}
		}
	}

	/**
	 * If a listing gets trashed/deleted, the pack may need it's listing count changing
	 */
	public function wp_trash_post( $id ) {
		global $wpdb;

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'job_listing' === $post_type || 'resume' === $post_type ) {
				$package_product_id = get_post_meta( $id, '_package_id', true );
				$package_id         = get_post_meta( $id, '_user_package_id', true );
				$package_product    = get_post( $package_product_id );

				if ( $package_product_id ) {
					$subscription_type = $this->get_package_subscription_type( $package_product_id );

					if ( 'listing' === $subscription_type ) {
						$new_count = $wpdb->get_var( $wpdb->prepare( "SELECT package_count FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
						$new_count --;

						$wpdb->update(
							"{$wpdb->prefix}wcpl_user_packages",
							array(
								'package_count'  => max( 0, $new_count )
							),
							array(
								'id' => $package_id
							)
						);
					}
				}
			}
		}
	}

	/**
	 * If a listing gets restored, the pack may need it's listing count changing
	 */
	public function untrash_post( $id ) {
		global $wpdb;

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'job_listing' === $post_type || 'resume' === $post_type ) {
				$package_product_id = get_post_meta( $id, '_package_id', true );
				$package_id         = get_post_meta( $id, '_user_package_id', true );
				$package_product    = get_post( $package_product_id );

				if ( $package_product_id ) {
					$subscription_type = $this->get_package_subscription_type( $package_product_id );

					if ( 'listing' === $subscription_type ) {
						$package  = $wpdb->get_row( $wpdb->prepare( "SELECT package_count, package_limit FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
						$new_count = $package->package_count + 1;

						$wpdb->update(
							"{$wpdb->prefix}wcpl_user_packages",
							array(
								'package_count'  => min( $package->package_limit, $new_count )
							),
							array(
								'id' => $package_id
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Subscription has expired - cancel job packs
	 */
	public function subscription_ended( $subscription ) {
		global $wpdb;

		foreach ( $subscription->get_items() as $item ) {
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );
			$legacy_id         = isset( $subscription->order->id ) ? $subscription->order->id : $subscription->id;
			$user_package      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE order_id IN ( %d, %d ) AND product_id = %d;", $subscription->id, $legacy_id, $item['product_id'] ) );

			if ( $user_package ) {
				// Delete the package
				$wpdb->delete(
					"{$wpdb->prefix}wcpl_user_packages",
					array(
						'id' => $user_package->id
					)
				);

				// Expire listings posted with package
				if ( 'listing' === $subscription_type ) {
					$listing_ids = wc_paid_listings_get_listings_for_package( $user_package->id );

					foreach ( $listing_ids as $listing_id ) {
						$listing = array( 'ID' => $listing_id, 'post_status' => 'expired' );
						wp_update_post( $listing );
					}
				}
			}
		}

		delete_post_meta( $subscription->id, 'wc_paid_listings_subscription_packages_processed' );
	}

	/**
	 * Subscription activated
	 */
	public function subscription_activated( $subscription ) {
		global $wpdb;

		if ( get_post_meta( $subscription->id, 'wc_paid_listings_subscription_packages_processed', true ) ) {
			return;
		}

		// Remove any old packages for this subscription
		$legacy_id = isset( $subscription->order->id ) ? $subscription->order->id : $subscription->id;
		$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", array( 'order_id' => $legacy_id ) );
		$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", array( 'order_id' => $subscription->id ) );

		foreach ( $subscription->get_items() as $item ) {
			$product           = wc_get_product( $item['product_id'] );
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );

			// Give user packages for this subscription
			if ( $product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) && $subscription->get_user_id() && ! isset( $item['switched_subscription_item_id'] ) ) {

				// Give packages to user
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = wc_paid_listings_give_user_package( $subscription->get_user_id(), $product->id, $subscription->id );
				}

				// Approve job or resume with new package
				if ( isset( $item['job_id'] ) ) {
					$job = get_post( $item['job_id'] );

					if ( in_array( $job->post_status, array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_job_listing_with_package( $job->ID, $subscription->get_user_id(), $user_package_id );
					}
				} elseif( isset( $item['resume_id'] ) ) {
					$resume = get_post( $item['resume_id'] );

					if ( in_array( $resume->post_status, array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_resume_with_package( $resume->ID, $subscription->get_user_id(), $user_package_id );
					}
				}
			}
		}

		update_post_meta( $subscription->id, 'wc_paid_listings_subscription_packages_processed', true );
	}

	/**
	 * Subscription renewed - renew the job pack
	 */
	public function subscription_renewed( $subscription ) {
		global $wpdb;

		foreach ( $subscription->get_items() as $item ) {
			$product           = wc_get_product( $item['product_id'] );
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );
			$legacy_id         = isset( $subscription->order->id ) ? $subscription->order->id : $subscription->id;

			// Renew packages which refresh every term
			if ( 'package' === $subscription_type ) {
				if ( ! $wpdb->update(
					"{$wpdb->prefix}wcpl_user_packages",
					array(
						'package_count'  => 0
					),
					array(
						'order_id'   => $subscription_id,
						'product_id' => $item['product_id']
					)
				) ) {
					wc_paid_listings_give_user_package( $subscription->get_user_id(), $item['product_id'], $subscription->id );
				}

			// Otherwise the listings stay active, but we can ensure they are synced in terms of featured status etc
			} else {
				if ( $user_package_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wcpl_user_packages WHERE order_id IN ( %d, %d ) AND product_id = %d;", $subscription->id, $legacy_id, $item['product_id'] ) ) ) {
					foreach ( $user_package_ids as $user_package_id ) {
						$package = wc_paid_listings_get_user_package( $user_package_id );

						if ( $listing_ids = wc_paid_listings_get_listings_for_package( $user_package_id ) ) {
							foreach ( $listing_ids as $listing_id ) {
								// Featured or not
								update_post_meta( $listing_id, '_featured', $package->is_featured() ? 1 : 0 );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * When switching a subscription we need to update old listings.
	 *
	 * No need to give the user a new package; that is still handled by the orders class.
	 */
	public function subscription_item_switched( $order, $subscription, $new_order_item_id, $old_order_item_id ) {
		global $wpdb;

		$new_order_item = WC_Subscriptions_Order::get_item_by_id( $new_order_item_id );
		$old_order_item = WC_Subscriptions_Order::get_item_by_id( $old_order_item_id );

		$new_subscription = (object) array(
			'id'           => $subscription->id,
			'subscription' => $subscription,
			'product_id'   => $new_order_item['product_id'],
			'product'      => wc_get_product( $new_order_item['product_id'] ),
			'type'         => $this->get_package_subscription_type( $new_order_item['product_id'] )
		);

		$old_subscription = (object) array(
			'id'           => $subscription->id,
			'subscription' => $subscription,
			'product_id'   => $old_order_item['product_id'],
			'product'      => wc_get_product( $old_order_item['product_id'] ),
			'type'         => $this->get_package_subscription_type( $old_order_item['product_id'] )
		);

		$this->switch_package( $subscription->get_user_id(), $new_subscription, $old_subscription );
	}

	/**
	 * When switching a subscription we need to update old listings.
	 *
	 * No need to give the user a new package; that is still handled by the orders class.
	 */
	public function subscription_switched( $subscription, $new_order_item, $old_order_item ) {
		global $wpdb;

		$new_subscription = (object) array(
			'id'         => $subscription->id,
			'product_id' => $new_order_item['product_id'],
			'product'    => wc_get_product( $new_order_item['product_id'] ),
			'type'       => $this->get_package_subscription_type( $new_order_item['product_id'] )
		);

		$old_subscription = (object) array(
			'id'         => $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d ", $new_order_item['switched_subscription_item_id'] ) ),
			'product_id' => $old_order_item['product_id'],
			'product'    => wc_get_product( $old_order_item['product_id'] ),
			'type'       => $this->get_package_subscription_type( $old_order_item['product_id'] )
		);

		$this->switch_package( $subscription->get_user_id(), $new_subscription, $old_subscription );
	}

	/**
	 * Handle Switch Event
	 */
	public function switch_package( $user_id, $new_subscription, $old_subscription ) {
		global $wpdb;

		// Get the user package
		$legacy_id    = isset( $old_subscription->subscription->order->id ) ? $old_subscription->subscription->order->id : $old_subscription->id;
		$user_package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE order_id IN ( %d, %d ) AND product_id = %d;", $old_subscription->id, $legacy_id, $old_subscription->product_id ) );

		if ( $user_package ) {
			// If invalid, abort
			if ( ! $new_subscription->product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) ) {
				return false;
			}

			// Give new package to user
			$switching_to_package_id = wc_paid_listings_give_user_package( $user_id, $new_subscription->product_id, $new_subscription->id );

			// Upgrade?
			$is_upgrade = ( 0 === $new_subscription->product->get_limit() || $new_subscription->product->get_limit() >= $user_package->package_count );

			// Delete the old package
			$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", array( 'id' => $user_package->id ) );

			// Update old listings
			if ( 'listing' === $new_subscription->type && $switching_to_package_id ) {
				$listing_ids = wc_paid_listings_get_listings_for_package( $user_package->id );

				foreach ( $listing_ids as $listing_id ) {
					// If we are not upgrading, expire the old listing
					if ( ! $is_upgrade ) {
						$listing = array( 'ID' => $listing_id, 'post_status' => 'expired' );
						wp_update_post( $listing );
					} else {
						wc_paid_listings_increase_package_count( $user_id, $switching_to_package_id );
						// Change the user package ID and package ID
						update_post_meta( $listing_id, '_user_package_id', $switching_to_package_id );
						update_post_meta( $listing_id, '_package_id', $new_subscription->product_id );
					}

					// Featured or not
					update_post_meta( $listing_id, '_featured', $new_subscription->product->is_featured() ? 1 : 0 );

					// Fire action
					do_action( 'wc_paid_listings_switched_subscription', $listing_id, $user_package );
				}
			}
		}
	}
}
WC_Paid_Listings_Subscriptions::get_instance();
