<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Description of BP_WP_Job_Manager
 *
 * @author kishore
 */
if ( ! class_exists( 'BP_WP_Job_Manager' ) ) {
    
    class BP_WP_Job_Manager {
        
        function __construct() {
        	global $bp;
           
            // Define constants
            $this->define_constants();

            // Include required files
            $this->includes();
        }
        
        function includes() {
        	// Includes
			include( BUDDYPRESS_WP_JOB_MANAGER .'bp-wp-job-manager/bp-wp-job-manager-loader.php' );
			include( BUDDYPRESS_WP_JOB_MANAGER .'bp-wp-job-manager/bp-wp-job-manager-functions.php' );
			include( BUDDYPRESS_WP_JOB_MANAGER .'bp-wp-job-manager/bp-wp-job-manager-screens.php' );
			
			include( BUDDYPRESS_WP_JOB_MANAGER .'bp-wp-job-manager-activity/bp-wp-job-manager-activity.php' );
			include( BUDDYPRESS_WP_JOB_MANAGER .'bp-wp-job-manager-activity/bp-wp-job-manager-activity-functions.php' );
			
			// Includes shortcode-action-handler
			include( BUDDYPRESS_WP_JOB_MANAGER .'shortcode-action-handler/class-bp-wp-job-manager-shortcode-action-handler.php' );
            
        }
        
        function define_constants() {
        	// for constants
        }
    
    }
    
}

$GLOBALS['bp_wp_job_manager'] = new BP_WP_Job_Manager();