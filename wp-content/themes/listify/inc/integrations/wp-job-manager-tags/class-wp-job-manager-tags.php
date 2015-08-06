<?php
/**
 * WP Job Manager - Tags
 */

class Listify_WP_Job_Manager_Tags extends listify_Integration {

	public function __construct() {
		$this->includes = array();

		$this->integration = 'wp-job-manager-tags';

		parent::__construct();
	}

	public function setup_actions() {
		add_filter( 'job_filter_tag_cloud', array( $this, 'job_filter_tag_cloud' ) );
		add_filter( 'job_manager_settings', array( $this, 'settings' ), 11 );
	}

	public function job_filter_tag_cloud( $atts ) {
		$atts[ 'separator' ] = '';

		return $atts;
	}

	public function settings( $fields ) {
		$settings = $fields[ 'job_listings' ][1];

		foreach ( $settings as $key => $value ) {
			if ( 'job_manager_enable_tag_archive' == $value[ 'name' ] ) {
				unset( $fields[ 'job_listings' ][1][ $key ] );
			}
		}

		return $fields;
	}

}

$GLOBALS[ 'listify_job_manager_tags' ] = new Listify_WP_Job_Manager_Tags();
