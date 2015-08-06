<?php
/*
Plugin Name: Will - Woocommerce Snippets
Plugin URI:
Description: This is a site specific plugin that's used to store the snippets to modify the core Woocmmerce functionality or edit the add on functionality
Version: 0.1
Author: Will MacMillan
Author URI: http://www.facebook.com/macmillan.will
Text Domain: 
Domain Path: 
*/
/* Start Adding Functions Below this Line */

/*
*
*Woocommerce specfic customizations
*
*/

//removes related products from shop
function wc_remove_related_products( $args ) {
  return array();
}
add_filter('woocommerce_related_products_args','wc_remove_related_products', 10); 

/*removes the additional comments form from checkout on woocommerce*/
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 1);



?>