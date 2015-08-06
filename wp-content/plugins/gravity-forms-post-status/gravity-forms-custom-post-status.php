<?php
/*
Plugin Name: Gravity Forms - Custom Post Statuses
Plugin URI:
Description: 
Version: 0.1
Author: Will MacMillan
Author URI: http://www.facebook.com/macmillan.will
Text Domain: 
Domain Path: 
*/
/* Start Adding Functions Below this Line */


/*this plugin creates the custom post status of "pending payment" for the WP Job Manager listing submission*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'gform_post_status_options', 'add_custom_post_status' );
function add_custom_post_status( $post_status_options ) {
    $post_status_options['pending_payment'] = 'Pending payment';
    return $post_status_options;
}


/* Stop Adding Functions Below this Line */
?>