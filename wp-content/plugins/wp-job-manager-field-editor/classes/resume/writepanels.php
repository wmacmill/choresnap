<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Resume_Manager_Writepanels' ) )
	include( RESUME_MANAGER_PLUGIN_DIR . '/includes/admin/class-wp-resume-manager-writepanels.php' );

class WP_Job_Manager_Field_Editor_Resume_Writepanels extends WP_Resume_Manager_Writepanels {


	function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'rebuild_meta_boxes' ), 100 );

	}

	function rebuild_meta_boxes(){

		remove_meta_box( 'resume_data', 'resume', 'normal' );
		remove_meta_box( 'resume_url_data', 'resume', 'side' );
		remove_meta_box( 'resume_education_data', 'resume', 'normal' );
		remove_meta_box( 'resume_experience_data', 'resume', 'normal' );

		$this->add_meta_boxes();

	}

}