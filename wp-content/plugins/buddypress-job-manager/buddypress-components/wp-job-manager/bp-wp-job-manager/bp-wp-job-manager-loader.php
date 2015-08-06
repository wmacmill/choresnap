<?php

/**
 * BuddyPress BP_WP_Job_Manager_Component Loader
 *
 * @package BuddyPress
 * @subpackage SettingsLoader
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_WP_Job_Manager_Component extends BP_Component {

	/**
	 * Start the Job Manager component creation process
	 *
	 * @since BuddyPress (1.5)
	 */
	public function __construct() {
		parent::start(
			'job-manager',
			__( 'Job Manager', 'buddypress-job-manager' ),
			plugin_dir_path( __FILE__ ),
			array(
				'adminbar_myaccount_order' => 100
			)
		);
	}

	/**
	 * Include files
	 *
	 * @global BuddyPress $bp The one true BuddyPress instance
	 */
	public function includes( $includes = array() ) {
		parent::includes( array(
			'actions',
			'screens',
			'template',
			'functions',
		) );
	}

	/**
	 * Setup globals
	 *
	 * The BP_WP_JOB_MANAGER_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since BuddyPress (1.5)
	 */
	public function setup_globals( $args = array() ) {

		// Define a slug, if necessary
		if ( !defined( 'BP_WP_JOB_MANAGER_SLUG' ) )
			define( 'BP_WP_JOB_MANAGER_SLUG', $this->id );

		// All globals for settings component.
		parent::setup_globals( array(
			'slug'          => BP_WP_JOB_MANAGER_SLUG,
			'has_directory' => false,
		) );
	}

	/**
	 * Set up navigation.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
		
		if ( ! class_exists( 'WP_Job_Manager' ) )
			return;

		// Add the Job Manager navigation item
		$main_nav = array(
			'name'                    => __( 'Job Manager', 'buddypress-job-manager' ),
			'slug'                    => $this->slug,
			'position'                => 100,
			'show_for_displayed_user' => bp_core_can_edit_settings(),
			'screen_function'         => 'bp_wp_job_manager_screen_job_dashboard',
			'default_subnav_slug'     => 'job-dashboard'
		);

		// Determine user to use
		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}

		$job_manager_link = trailingslashit( $user_domain . $this->slug );

		// Add Job Dashboard nav item.
		$sub_nav[] = array(
			'name'            => __( 'Job Dashboard', 'buddypress-job-manager' ),
			'slug'            => 'job-dashboard',
			'parent_url'      => $job_manager_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_wp_job_manager_screen_job_dashboard',
			'position'        => 10,
			'user_has_access' => bp_core_can_edit_settings()
		);
		
		// Add Jobs nav item
		if ( get_current_user_id() == bp_displayed_user_id() ) {
			$sub_nav[] = array(
				'name'            => __( 'Jobs', 'buddypress-job-manager' ),
				'slug'            => 'jobs',
				'parent_url'      => $job_manager_link,
				'parent_slug'     => $this->slug,
				'screen_function' => 'bp_wp_job_manager_screen_jobs',
				'position'        => 20,
				'user_has_access' => bp_core_can_edit_settings()
			);
		}
		
		// Add My Bookmarks nav item.
		if ( class_exists( 'WP_Job_Manager_Bookmarks' ) && get_current_user_id() == bp_displayed_user_id() ) {
			$sub_nav[] = array(
				'name'            => __( 'My Bookmarks', 'buddypress-job-manager' ),
				'slug'            => 'my-bookmarks',
				'parent_url'      => $job_manager_link,
				'parent_slug'     => $this->slug,
				'screen_function' => 'bp_wp_job_manager_screen_bookmarks',
				'position'        => 50,
				'user_has_access' => bp_core_can_edit_settings()
			);
		}
		
		// Add Job Alerts nav item.
		if ( class_exists( 'WP_Job_Manager_Alerts' ) && get_current_user_id() == bp_displayed_user_id() ) {
			$sub_nav[] = array(
				'name'            => __( 'Job Alerts', 'buddypress-job-manager' ),
				'slug'            => 'job-alerts',
				'parent_url'      => $job_manager_link,
				'parent_slug'     => $this->slug,
				'screen_function' => 'bp_wp_job_manager_screen_job_alerts',
				'position'        => 60,
				'user_has_access' => bp_core_can_edit_settings()
			);
		}
		
		// Add Post a Job nav item.
		$sub_nav[] = array(
			'name'            => __( 'Post a Job', 'buddypress-job-manager' ),
			'slug'            => 'post-a-job',
			'parent_url'      => $job_manager_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_wp_job_manager_screen_submit_job_form',
			'position'        => 70,
			'user_has_access' => bp_core_can_edit_settings()
		);
		
		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the Toolbar
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		
		if ( ! class_exists( 'WP_Job_Manager' ) )
			return;

		// The instance
		$bp = buddypress();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$user_domain   = bp_loggedin_user_domain();
			$job_manager_link = trailingslashit( $user_domain . $this->slug );

			// Add main Job Manager menu
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'Job Manager', 'buddypress-job-manager' ),
				'href'   => trailingslashit( $job_manager_link )
			);
			
			// Job Dashboard
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-job-dashboard',
				'title'  => __( 'Job Dashboard', 'buddypress-job-manager' ),
				'href'   => trailingslashit( $job_manager_link . 'job-dashboard' )
			);
			
			// Jobs
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-jobs',
				'title'  => __( 'Jobs', 'buddypress-job-manager' ),
				'href'   => trailingslashit( $job_manager_link . 'jobs' )
			);
			
			if ( class_exists( 'WP_Job_Manager_Bookmarks' ) ) {
				// My Bookmarks
				$wp_admin_nav[] = array(
					'parent' => 'my-account-' . $this->id,
					'id'     => 'my-account-' . $this->id . '-my-bookmarks',
					'title'  => __( 'My Bookmarks', 'buddypress-job-manager' ),
					'href'   => trailingslashit( $job_manager_link . 'my-bookmarks' )
				);
			}
			
			if ( class_exists( 'WP_Job_Manager_Alerts' ) ) {
				// Job Alerts
				$wp_admin_nav[] = array(
					'parent' => 'my-account-' . $this->id,
					'id'     => 'my-account-' . $this->id . '-job-alerts',
					'title'  => __( 'Job Alerts', 'buddypress-job-manager' ),
					'href'   => trailingslashit( $job_manager_link . 'job-alerts' )
				);
			}
			
			// Job Alerts
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-post-a-job',
				'title'  => __( 'Post a Job', 'buddypress-job-manager' ),
				'href'   => trailingslashit( $job_manager_link . 'post-a-job' )
			);
			
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}
}

new BP_WP_Job_Manager_Component();