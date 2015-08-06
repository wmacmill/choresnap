<?php

/**
 * BuddyPress Job Manager Screens
 *
 * @package Buddypress Job Manager
 * @subpackage Job Manager Screens
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Show the jobs Job Manager template
 *
 * @since Job Manager (1.0.0)
 */
function bp_wp_job_manager_screen_jobs() {

	if ( bp_action_variables() ) {
		bp_do_404();
		return;
	}

	bp_core_load_template( apply_filters( 'bp_wp_job_manager_screen_jobs', 'members/single/home' ) );
	add_action('bp_template_content','load_jobs_template');
}

function load_jobs_template() {
    // include  file or echo do_shortcode
    //echo do_shortcode( '[jobs]' );
	get_job_manager_template_part( 'bp-jobs', 'content', 'buddypress_job_manager', BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
}

/**
 * Show the job_dashboard Job Manager template
 *
 * @since Job Manager (1.0.0)
 */
function bp_wp_job_manager_screen_job_dashboard() {

	if ( bp_action_variables() ) {
		bp_do_404();
		return;
	}

	bp_core_load_template( apply_filters( 'bp_wp_job_manager_screen_job_dashboard', 'members/single/home' ) );
	add_action('bp_template_content','load_job_dashboard_template');
}

function load_job_dashboard_template() {
    // include  file or echo do_shortcode
	add_filter( 'job_manager_get_dashboard_jobs_args', 'function_to_change_dashboard_jobs_args', 10, 1 );
	//echo do_shortcode( '[job_dashboard]' );
	get_job_manager_template_part( 'bp-job-dashboard', 'content', 'buddypress_job_manager', BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
	remove_filter( 'job_manager_get_dashboard_jobs_args', 'function_to_change_dashboard_jobs_args', 10, 1 );
}

/**
 * Show the submit_job_form Job Manager template
 *
 * @since Job Manager (1.0.0)
 */
function bp_wp_job_manager_screen_submit_job_form() {

	if ( bp_action_variables() ) {
		bp_do_404();
		return;
	}

	bp_core_load_template( apply_filters( 'bp_wp_job_manager_screen_submit_job_form', 'members/single/home' ) );
	add_action('bp_template_content','load_submit_job_form_template');
}

function load_submit_job_form_template() {
    // include  file or echo do_shortcode
	//echo do_shortcode( '[submit_job_form]' );
	get_job_manager_template_part( 'bp-submit-job-form', 'content', 'buddypress_job_manager', BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
}

/**
 * Show the my_bookmarks Job Manager template
 *
 * @since Job Manager (1.0.0)
 */
function bp_wp_job_manager_screen_bookmarks() {

	if ( bp_action_variables() ) {
		bp_do_404();
		return;
	}

	// Load the template
	bp_core_load_template( apply_filters( 'bp_wp_job_manager_screen_my_bookmarks', 'members/single/home' ) );
	add_action('bp_template_content','load_jobs_bookmarks_template');
}

function load_jobs_bookmarks_template() {
    // include  file or echo do_shortcode
    //echo do_shortcode( '[my_bookmarks]' );
	get_job_manager_template_part( 'bp-my-bookmarks', 'content', 'buddypress_job_manager', BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
}

/**
 * Show the job_alerts Job Manager template
 *
 * @since Job Manager (1.0.0)
 */
function bp_wp_job_manager_screen_job_alerts() {

	if ( bp_action_variables() ) {
		bp_do_404();
		return;
	}

	// Load the template
	bp_core_load_template( apply_filters( 'bp_wp_job_manager_screen_job_alerts', 'members/single/home' ) );
	add_action('bp_template_content','load_jobs_alerts_template');
}

function load_jobs_alerts_template() {
    // include  file or echo do_shortcode
    //echo do_shortcode( '[job_alerts]' );
	get_job_manager_template_part( 'bp-job-alerts', 'content', 'buddypress_job_manager', BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR . '/templates/' );
}