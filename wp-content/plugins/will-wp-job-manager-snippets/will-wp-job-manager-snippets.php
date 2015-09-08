<?php
/*
Plugin Name: Will - WP Job Manager Snippets
Plugin URI:
Description: This is a site specific plugin that's used to store the snippets to modify the core WP Job Manager functionality or edit the add on functionality
Version: 0.1
Author: Will MacMillan
Author URI: http://www.facebook.com/macmillan.will
Text Domain: 
Domain Path: 
*/
/* Start Adding Functions Below this Line */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*edits to core plugin functionality*/

/*edits to add ons*/

/*Resume add on snippets*/
/*Changes the slug for submitted resumes*/
add_filter( 'submit_resume_form_save_resume_data', 'custom_submit_resume_form_save_resume_data', 10, 5 );
 
function custom_submit_resume_form_save_resume_data( $data, $post_title, $post_content, $status, $values ) {
 
	// No random prefix - just use post title as the permalink/slug
	//$data['post_name'] = sanitize_title( $post_title );
	
	// This line appends the location of the user
	//$data['post_name'] .= '-' . sanitize_title( $values['resume_fields']['candidate_location'] );
	
	return $data;
}

/** Plugin Name: Stop WooCommerce Subscriptions Changing a User's Role on checkout because it will be set by WP Job Manager instead */

add_filter( 'woocommerce_subscriptions_update_users_role', '__return_false', 100 );


//settings for customizing the phone field on the resume application/edit form
add_filter( 'job_manager_field_editor_phone_args', 'my_custom_phone_args' );
 
function my_custom_phone_args( $args ){
 
  $args['preferredCountries'] = array( 'us', 'ca' );
  $args['defaultCountry'] = 'ca';
 
  return $args;
}

//removes the job description
remove_filter ('the_job_description', get_the_content() );

//removes the "order again" button from the receipt page
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );


//this tests the adding of meta to the single chore posting
add_action( 'single_job_listing_meta_end', 'display_building_type' );

function display_building_type() {
  global $post;

  $building_type = get_post_meta( $post->ID, 'building_type', true );

  if ( $building_type ) {
    echo '<li>' . __( 'Building Type:' ) . ' ' . $building_type . '</li>';
  }
}

/************** this is to hijack the checkout process in wc paid listings. If package is the free one then set to pending & go to my account ***********/
add_action ( 'wcpl_process_package_for_job_listing', 'wp_job_manager_wc_paid_listings_free_package', 10, 3 );

function wp_job_manager_wc_paid_listings_free_package ( $package_id, $is_user_package, $job_id ) {   
    global $woocommerce;

    $valid = true;
    $free_listing = 1040; //post id of the free listing package
    
    if ( $free_listing == $package_id ) {
        $job_expiry_date      = date('Y-m-d', strtotime("+5 days"));
        update_post_meta( $job_id, '_job_expires', $job_expiry_date );        
        
        $update_job                = array();
        $update_job['ID']          = $job_id;
        $update_job['post_status'] = 'pending';
        wp_update_post( $update_job );

        //empty the cart
        $woocommerce->cart->empty_cart();

        //remove the "added to cart" message
        wc_clear_notices();

        //wrap it up and send them to the my chores page
        $url = site_url ( '/my-account/my-chores/' );
        wp_redirect ( $url );
        exit;
    }
    return $valid;
}
/***********end wp job manager paid listings hijack ************************/

/*changes the resume slug from 'resume' to 'company' - need to resave permalinks if something is wrong*/
function change_resume_listing_slug( $args ) {
  $args['rewrite']['slug'] = _x( 'company', 'Resume permalink - resave permalinks after changing this', 'job_manager' );
  return $args;
}
add_filter( 'register_post_type_resume', 'change_resume_listing_slug' );

function attach_images_resume () {
  $attach = true;
  return $attach;
}
add_filter ('resume_manager_attach_uploaded_files', 'attach_images_resume');


/* Stop Adding Functions Below this Line */
?>