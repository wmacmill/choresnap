<?php
/**
	Plugin Name: WooCommerce - Autocomplete Order
	Plugin URI: 
	Description: Do you hate WooCommerce for obliging you to manually approve every order placed for non-downloadable goods? This plugin is the answer, since allows to automatically mark orders for **virtual** products as Completed after a successful payment (e.g. with PayPal or Credit Card).
	Version: 1.1.1
	Author: Mirko Grewing
	Author URI: http://www.mirkogrewing.it	
		
		Copyright: © 2013 Mirko Grewing (email : mirko@grewing.co.uk)	
		License: GNU General Public License v3.0	
		License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    load_plugin_textdomain('wooExtraOptions', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    if (!class_exists('MG_Woo_Extra_Options')) {
        /**
         * Main Loader Class
         * Used to extend WooCommerce settings.
         *
         * @category  Class
         * @package   Woocommerce_Autocomplete_Order
         * @author    Mirko Grewing <mirko@grewing.co.uk>
         * @copyright 2012-2015 Mirko Grewing
         * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
         * @version   1.1.1
         * @link      http://www.mirkogrewing.it
         * @since     Class available since Release 0.1
         *
         */
        class MG_Woo_Extra_Options
        {
            /**
             * $id 
             * holds settings tab id
             * @var string
             */
            public $id = 'mg_woo_eo';
            
            /**
             * __construct
             * class constructor will set the needed filter and action hooks
             */
            function __construct()
            {
				if (is_admin()) {
					//add settings tab
					add_filter('woocommerce_settings_tabs_array', array($this,'woocommerce_settings_tabs_array'), 50);
					//show settings tab
					add_action('woocommerce_settings_tabs_'.$this->id, array($this,'show_settings_tab'));
					//save settings tab 
					add_action('woocommerce_update_options_'.$this->id, array($this,'update_settings_tab'));
					//add tabs select field
					add_action('woocommerce_admin_field_'.$this->id,array($this, 'show_'.$this->id.'_field'), 10);
					//save tabs select field
					add_action('woocommerce_update_option_'.$this->id,array($this, 'save_'.$this->id.'_field'), 10);
				}
                add_action('init', array($this,'autocompleteOrders'), 0);
				
			}
			
			/**
			 * woocommerce_settings_tabs_array 
			 * Used to add a WooCommerce settings tab
			 * @param  array $settings_tabs
			 * @return array
			 */
			function woocommerce_settings_tabs_array( $settings_tabs ) {
				$settings_tabs[$this->id] = __('Woo Extra Options','wooExtraOptions');
				return $settings_tabs;
			}

			/**
			 * show_settings_tab
			 * Used to display the WooCommerce settings tab content
			 * @return void
			 */
			function show_settings_tab(){
				woocommerce_admin_fields($this->get_settings());
			}

			/**
			 * update_settings_tab
			 * Used to save the WooCommerce settings tab values
			 * @return void
			 */
			function update_settings_tab(){
				woocommerce_update_options($this->get_settings());
			}

			/**
			 * get_settings
			 * Used to define the WooCommerce settings tab fields
			 * @return void
			 */
			function get_settings(){
				$settings = array(
					'section_title' => array(
						'name'     => __('Autocomplete Orders','wooExtraOptions'),
						'type'     => 'title',
						'desc'     => '',
						'id'       => 'wc_'.$this->id.'_section_title'
					),
					'title' => array(
						'name'     => __('Mode', 'wooExtraOptions'),
						'type'     => 'select',
						'desc'     => __('Select the type of autocompletion you want to activate.', 'wooExtraOptions'),
						'desc_tip' => true,
						'default'  => 'off',
						'id'       => 'wc_'.$this->id.'_mode',
						'options' => array(
							'off'     => 'Off',
							'virtual' => 'Paid virtual products',
							'paid'    => 'All paid products',
							'all'     => 'All products'
						)
					),
					'section_end' => array(
						'type' => 'sectionend',
						'id'   => 'wc_'.$this->id.'_section_end'
					)
				);
				return apply_filters( 'wc_'.$this->id.'_settings', $settings );
			}

			/**
			 * autocompleteOrders 
			 * Autocomplete Orders
			 * @return void
			 */
			function autocompleteOrders()
			{
				$mode = get_option('wc_'.$this->id.'_mode');
				if ($mode == 'all') {
					add_action('woocommerce_thankyou', 'autocompleteAllOrders');
					/**
					 * autocompleteAllOrders 
					 * Register custom tabs Post Type
					 * @return void
					 */
					function autocompleteAllOrders($order_id)
					{
						global $woocommerce;

						if (!$order_id)
							return;
						$order = new WC_Order($order_id);
						$order->update_status('completed');
					}
				} elseif ($mode == 'paid') {
					add_filter('woocommerce_payment_complete_order_status', 'autocompletePaidOrders', 10, 2);
					/**
					 * autocompletePaidOrders 
					 * Register custom tabs Post Type
					 * @return void
					 */
					function autocompletePaidOrders($order_status, $order_id)
					{
						$order = new WC_Order($order_id);
						if ($order_status == 'processing' && ($order->status == 'on-hold' || $order->status == 'pending' || $order->status == 'failed')) {
							return 'completed';
						}
						return $order_status;
					}
				} elseif ($mode == 'virtual') {
					add_filter('woocommerce_payment_complete_order_status', 'autocompleteVirtualOrders', 10, 2);
					/**
					 * autocompleteVirtualOrders 
					 * Register custom tabs Post Type
					 * @return void
					 */
					function autocompleteVirtualOrders($order_status, $order_id)
					{
						$order = new WC_Order($order_id);
						if ('processing' == $order_status && ('on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status)) {
							$virtual_order = null;
							if (count($order->get_items()) > 0 ) {
								foreach ($order->get_items() as $item) {
									if ('line_item' == $item['type']) {
										$_product = $order->get_product_from_item($item);
										if (!$_product->is_virtual()) {
											$virtual_order = false;
											break;
										} else {
											$virtual_order = true;
										}
									}
								}
							}
							if ($virtual_order) {
								return 'completed';
							}
						}
						return $order_status;
					}
				}
			}
		}//end MG_Woo_Extra_Options class.
		new MG_Woo_Extra_Options();
	}
} elseif (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.1', '<')) {
	wc_add_notice(sprintf(__("This plugin requires WooCommerce 2.1 or higher!", "wooExtraOptions" ), 'error'));
} else {
    /**
     * Check if WooCommerce is up and running
     *
     * @return null
     */
    function checkWooNotices()
    {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            ob_start();
            ?><div class="error">
                <p><strong><?php _e('WARNING', 'wooExtraOptions'); ?></strong>: <?php _e('WooCommerce is not active and WooCommerce Autocomplete Order will not work!', 'wooExtraOptions'); ?></p>
            </div><?php
            echo ob_get_clean();
        }
    }
    add_action('admin_notices', 'checkWooNotices');
}
?>