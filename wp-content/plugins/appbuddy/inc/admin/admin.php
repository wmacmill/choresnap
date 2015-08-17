<?php
/*
 * Author: AppPresser
 * Author URI: http://appresser.com
 * License: GPLv2
 */


/**
 * AppBuddy_Admin_Settings class.
 *
 * @extends AppBuddy
 */
class AppBuddy_Admin_Settings extends AppBuddy {

	public static $instance = null;


	/**
	 * run function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function run() {
		if ( self::$instance === null )
			self::$instance = new self();

		return self::$instance;
	}


	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'apppresser_add_settings', array( $this, 'appbuddy_settings' ) );
	}


	/**
	 * appbuddy_settings function.
	 *
	 * @access public
	 * @param mixed $appp
	 * @return void
	 */
	public function appbuddy_settings( $appp ) {

		$appp->add_setting( 'paragraph', '',
			array(
				'type' => 'h3',
				'description' => 'AppBuddy enhances BuddyPress in AppPresser apps. Get <a href="http://apppresser.com/extensions/appcamera/" target="_blank">AppCamera</a> for your app and allow your site users to upload images.',
				'tab' => 'appbuddy'
			)
		);

		$appp->add_setting_tab( __( 'AppBuddy', 'appbuddy' ), 'appbuddy' );

		$appp->add_setting( self::APPP_KEY, __( 'AppBuddy License Key', 'appbuddy' ),
			array( 'type' => 'license_key',
			'tab' => 'appbuddy',
			'helptext' => __( 'Adding a license key enables automatic updates.', 'appbuddy' )
			)
		);

		$appp->add_setting( 'appcam_appbuddy', __( 'AppCam', 'appbuddy' ),
			array( 'type' => 'checkbox',
			'tab' => 'appbuddy',
			'helptext' => __( 'Allow users to attach images to status updates.', 'appbuddy' )
			)
		);

		// check if app push for these options
		if ( class_exists( 'AppPresser_Notifications' ) ) {

			$appp->add_setting( 'apppush_appbuddy', __( 'AppPush', 'appbuddy' ),
				array( 'type' => 'checkbox',
				'tab' => 'appbuddy',
				'helptext' => __( 'Allow BuddyPress friend requests, private and public messages to send push notifications.', 'appbuddy' )
				)
			);

		}
	}

}
AppBuddy_Admin_Settings::run();
