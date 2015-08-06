<?php
/*
Plugin Name: Will - Deduct credits on applying
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

add_action ( 'new_job_application', 'mycred_deduct_on_apply', 10, 1);
function mycred_deduct_on_apply ( $user_id ) {

	// Bail if myCRED is not installed
	if ( ! function_exists( 'mycred' ) ) return;

	//get all your variables
	$user_ID = get_current_user_id();
	$post_ID = get_the_ID();
	$cred_value	= -1 * abs(get_post_meta( $post_ID, 'cred_field', true));//get_post_meta( $post_ID, 'cred_field', true);
	$current_balance = mycred_get_users_cred( $user_ID );

	
	//this checks if the new_job_application has been run once already because it runs twice on the submission
	//you need this check otherwise the person is billed twice for the submission
	 
	if ( did_action('new_job_application') === 1 ) {
		// Run myCRED	
		mycred_add(
		'application_deduction',
		$user_ID,
		$cred_value,
		'%plural% deduction for order chore #' . $post_ID,
		'',
		'',
		'mycred_default'
		);	
	}	
	
	
}


/* Stop Adding Functions Below this Line */
?>