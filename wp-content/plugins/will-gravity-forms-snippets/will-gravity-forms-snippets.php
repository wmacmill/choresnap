<?php
/*
Plugin Name: Will - Gravity Forms Snippets
Plugin URI:
Description: This plugin is meant to house all the gravity forms snippets that would normally reside in functions.php
Version: 1.0
Author: Will MacMillan
Author URI: 
Text Domain: 
Domain Path: 
*/
/* Start Adding Functions Below this Line */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*gravity forms auto login*/
add_action("gform_user_registered", "autologin", 10, 4);
function autologin($user_id, $config, $entry, $password) {
        wp_set_auth_cookie($user_id, false, '');
}

/*update the menu_order on the post submission*/
add_filter( 'gform_post_data', 'change_menu_order', 10, 3 );      
function change_menu_order( $post_data, $form, $entry ) {
    
    $form_id = $form['id'];
    $quote_forms = array( 6, 7 ); //needs to be updated with the form number of estimate forms
    if ( !in_array( $form_id, $quote_forms, true ) ){
       return $post_data;
    }

    $post_data['menu_order'] = 1;
    return $post_data;
}


//this updates the user meta fields based on the address information as well as the user type
add_action( 'gform_after_submission', 'input_fields', 10, 2 );
function input_fields( $entry, $form ) {
    if ( $form['id']==5){
        return;
    }
    else {
        $address_meta = $entry[68];
        $user_type = $entry[42];
        $user_region = $entry[19];
        $user_id = get_current_user_id();

        update_user_meta( $user_id, 'user_location', $address_meta );
        update_user_meta( $user_id, 'user_type', $user_type );
        update_user_meta( $user_id, 'user_region', $user_region );
    }
}


//this is to populate the advanced custom fields checkbox for cleaning add ons as it won't pull from the regular post meta
add_action("gform_after_submission", "acf_post_submission", 10, 2);
function acf_post_submission ($entry, $form)
{
   $post_id = $entry["post_id"];
   $date_value = get_post_custom_values("_chore_cleaning_addon_details", $post_id);
   update_field("field_556c818d2b7a1", $chore_date, $post_id);
}


/*This code allows for redirects programatically. Potentially use in the future with chore picker
add_filter( 'gform_confirmation', 'custom_confirmation', 10, 4 );
function custom_confirmation( $confirmation, $form, $entry, $ajax ) {
    if( $form['id'] == 5 ) {
         $confirmation = array( 'redirect' => site_url('/post-a-chore/apartment-cleaning') );
    }
    return $confirmation;
}*/

/*
 *
 * what is the performance hit associated with this? probalby minimal if it's only this one form I think?
 * this is required to get the 'ajaxify' links for apppresser to work with this form as it won't load on the page otherwise
 *
 */

function will_testing_gravity_ajax () {
    gravity_form_enqueue_scripts( 5, true );
}

add_action ('wp_head','will_testing_gravity_ajax');


/* Stop Adding Functions Below this Line */
?>