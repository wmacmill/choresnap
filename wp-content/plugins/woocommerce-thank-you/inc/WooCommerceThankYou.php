<?php

namespace WooCommerceThankYou;

class WooCommerceThankYou {
	/**
	 * The meta key used to store the products custom thank you page
	 */
	const META_KEY = 'custom-thank-you-page';

	/**
	 * The label input's name that is visible for the user
	 */
	const LABEL_INPUT_NAME = "product-thank-you-label";

	/**
	 * The ID input's name that is used to store the (invisible) ID of the local page
	 */
	const ID_INPUT_NAME = "product-thank-you";

	/**
	 * Method to kickstart the plugin. Can do whatever you want, but be careful to use it in compliance with WordPress
	 *
	 * @return void
	 */
	public static function start(){
		new WooCommerceThankYou;
	}

	/**
	 * Adds all necessary action hooks
	 */
	private function __construct(){
		// The field
		add_action('woocommerce_product_options_general_product_data', array($this, 'addProductGeneralTabField'));
		add_action('woocommerce_process_product_meta', array($this, 'saveProductGeneralTabField'));

		// The auto hinting
		add_action('admin_enqueue_scripts', array($this, 'includeJavascript'));
		add_action('wp_ajax_wc-thank-you-hint', array($this, 'hintThankYouPages'));

		// Custom thank you page handling after checkout
		add_action('woocommerce_thankyou', array($this, 'redirectThankYouPage'));
	}

	/**
	 * Includes the thank you hinting JS file on single edit pages
	 *
	 * @param string $hook
	 */
	public function includeJavascript($hook){
		if($hook === "post.php"){
			wp_enqueue_script('woocommerce-thank-you-hinting', plugins_url('assets/js/thank-you.js', __DIR__), array('jquery', 'jquery-ui-autocomplete'));
		}
	}

	/**
	 * Prints out the hints of pages in JSON format
	 */
	public function hintThankYouPages(){
		if(isset($_REQUEST['search'])){
			$search = $_REQUEST['search'];
		} else {
			$search = '';
		}

		$query = new \WP_Query(array(
			'post_type' => 'page',
		    'posts_per_page' => 10,
		    's' => $search
		                       ));

		$result = array();

		foreach($query->posts as $post){
			$result[] = array('label' => $post->post_title, 'value' => $post->ID);
		}

		echo json_encode(array('success' => true, 'data' => $result));
		die();
	}

	/**
	 * Sets up the text input field used for selecting the thank you page
	 */
	public function addProductGeneralTabField(){
		global $post;

		$labelFieldValue = "";
		$IDFieldValue = "";

		$metaValue = get_post_meta($post->ID, self::META_KEY, true);

		if(!empty($metaValue)){
			if((int) $metaValue !== 0){
				$IDFieldValue = $metaValue;
				$labelFieldValue = get_the_title((int) $metaValue);
			} else {
				$labelFieldValue = $metaValue;
			}
		}

		echo "<div class=\"options_group\">";
		woocommerce_wp_text_input(array('placeholder' => __('Type to get hints', 'woocommerce-thank-you'), 'id' => self::LABEL_INPUT_NAME, 'label' => __('Thank you page', 'woocommerce-thank-you'), 'value' => $labelFieldValue));
		woocommerce_wp_hidden_input(array('id' => self::ID_INPUT_NAME, 'value' => $IDFieldValue));
		echo "</div>";
	}

	/**
	 * Saves the contents from the thank you page field
	 */
	public function saveProductGeneralTabField($id){
		if(!isset($_REQUEST[self::ID_INPUT_NAME]) || !isset($_REQUEST[self::LABEL_INPUT_NAME])){
			new \WP_Error('Necessary field values are not present');
			return;
		}

		$thankYouPage = $_REQUEST[self::ID_INPUT_NAME];
		$thankYouPageLabel = trim($_REQUEST[self::LABEL_INPUT_NAME]);

		if(strpos($thankYouPageLabel, 'http') === 0){
			update_post_meta($id, self::META_KEY, $thankYouPageLabel);
		} else if(!empty($thankYouPage)){
			update_post_meta($id, self::META_KEY, $thankYouPage);
		} else {
			update_post_meta($id, self::META_KEY, '');
		}
	}

	/**
	 * Redirects to the proper selected thank you page (if any)
	 *
	 * @param int $orderID
	 */
	public function redirectThankYouPage($orderID){
		$order = wc_get_order($orderID);
		$items = $order->get_items();

		if(count($items) === 1){
			$keys = array_keys($items);
			$thankYouPage = get_post_meta($items[$keys[0]]['product_id'], self::META_KEY, true);

			if(!empty($thankYouPage)){
				if((int) $thankYouPage !== 0){
					$page = get_permalink((int) $thankYouPage);
					wp_redirect($page);
				} else {
					wp_redirect($thankYouPage);
				}
			}
		}
	}
}