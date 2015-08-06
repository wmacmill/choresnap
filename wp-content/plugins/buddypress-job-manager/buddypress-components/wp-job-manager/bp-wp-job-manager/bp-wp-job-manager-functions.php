<?php

/**
 * BuddyPress Settings Functions
 *
 * @package BuddyPress
 * @subpackage SettingsFunctions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function function_to_change_dashboard_jobs_args( $args ) {
    if ( is_buddypress() ) {
    	// ....If not show the job dashboard
		$posts_per_page = 25;
		$args     = array(
			'post_type'           => 'job_listing',
			'post_status'         => array( 'publish', 'expired', 'pending' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $posts_per_page,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * $posts_per_page,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => bp_displayed_user_id()
		);
		
		return $args;
    }
}

add_filter('job_manager_pagination_args', 'function_to_change_job_manager_pagination_args',10,1);

function function_to_change_job_manager_pagination_args($args) {
	if ( is_buddypress() ) {
		$args_new = array(
			'base' 	 => str_replace( '/page/999999999/', '?paged=%#%', get_pagenum_link( 999999999 ) ),
			'format' => '?paged=%#%',
		);
		$args = array_merge($args,$args_new );
		return $args;
	} else {
		return $args;
	}
	
}