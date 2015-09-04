<?php

class Jobify_WP_Job_Manager_Tags {

	public function __construct() {
		require_once( get_template_directory() . '/inc/integrations/wp-job-manager-tags/widgets/class-widget-job-tags.php' );

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	public function widgets_init() {
		register_widget( 'Jobify_Widget_Job_Tags' );
	}

}

$GLOBALS[ 'jobify_job_manager_tags' ] = new Jobify_WP_Job_Manager_Tags();
