<?php
/*
Plugin Name: Will - Enhanced User Search
Plugin URI:
Description: 
Version: 0.1
Author: Will MacMillan
Author URI: http://www.facebook.com/macmillan.will
Text Domain: 
Domain Path: 
*/
/* Start Adding Functions Below this Line */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/*this is to enhance user searching*/
add_action( 'pre_user_query', 'extended_user_search' );

function extended_user_search( $user_query ){
    // Make sure this is only applied to user search
    if ( $user_query->query_vars['search'] ){
        $search = trim( $user_query->query_vars['search'], '*' );
        if ( $_REQUEST['s'] == $search ){
            global $wpdb;
 
            $user_query->query_from .= " JOIN {$wpdb->usermeta} MF ON MF.user_id = {$wpdb->users}.ID AND MF.meta_key = 'first_name'";
            $user_query->query_from .= " JOIN {$wpdb->usermeta} ML ON ML.user_id = {$wpdb->users}.ID AND ML.meta_key = 'last_name'";
            //$user_query->query_from .= " JOIN {$wpdb->usermeta} ML ON ML.user_id = {$wpdb->users}.ID AND ML.meta_key = '_candidate_phone'";

            $user_query->query_where = 'WHERE 1=1' . $user_query->get_search_sql( $search, array( 'user_login', 'user_email', 'user_nicename', 'MF.meta_value', 'ML.meta_value' ), 'both' );
        }
    }
}


/* Stop Adding Functions Below this Line */
?>