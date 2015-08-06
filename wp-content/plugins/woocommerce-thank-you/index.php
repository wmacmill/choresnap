<?php

/**
 * Plugin Name: WooCommerce Custom Thank You Page
 * Plugin URI: https://preview.wphp.info/woocommerce
 * Description: An extension to the WordPress WooCommerce plugin that enables you to assign custom thank you pages to any product
 * Version: 1.0
 * Author: Sebastian Wasser
 * Author URI: https://wphp.info
 * Requires at least: 4.0
 * Tested up to: 4.1.1
 *
 * Text Domain: woocommerce-thank-you
 * Domain Path: /languages/
 */

// Include only the basics
require __DIR__."/inc/WooCommerceThankYou.php";

// Start the plugin
\WooCommerceThankYou\WooCommerceThankYou::start();