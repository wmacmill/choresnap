<?php
/*
Plugin Name: BuddyPress Job Manager
Plugin URI: http://buddypress-job-manager.opentuteplus.com/
Description: This plugin integrated WP Job Manager into your BuddyPress user profiles. This plugin needs BuddyPress and WP Job Manager to be installed.
Author: Kishore Sahoo
Author URI: http://blog.kishorechandra.co.in/
Version: 1.0.0
Requires at least: WP 3.8, BuddyPress 2.1.1
Tested up to: WP 4.1.1, BuddyPress 2.2.1
Network: true
Text Domain: buddypress-job-manager
Domain Path: /languages/

Copyright: 2015 Kishore Sahoo
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! defined( 'BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR ' ) ) {
	define( 'BUDDYPRESS_WP_JOB_MANAGER_PLUGIN_DIR',  untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'BUDDYPRESS_WP_JOB_MANAGER ' ) ) {
	define( 'BUDDYPRESS_WP_JOB_MANAGER', plugin_dir_path( __FILE__ ) . 'buddypress-components/wp-job-manager/' );
}

// I18n
add_action( 'plugins_loaded', 'buddypress_job_manager_load_textdomain' );
function buddypress_job_manager_load_textdomain() {
	load_plugin_textdomain( 'buddypress-job-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function init_wp_job_manager_component(){
    include( BUDDYPRESS_WP_JOB_MANAGER .'class-bp-wp-job-manager.php' );
}

add_action( 'bp_loaded', 'init_wp_job_manager_component', 40 );
