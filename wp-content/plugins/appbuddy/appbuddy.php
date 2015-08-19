<?php
/*
Plugin Name: AppBuddy
Plugin URI: http://apppresser.com
Description: AppBuddy enhances BuddyPress in AppPresser apps.
Text Domain: appbuddy
Domain Path: /languages
Version: 0.9.7
Author: AppPresser Team
Author URI: http://apppresser.com
License: GPLv2
*/


/**
 * AppBuddy class.
 */
class AppBuddy {

	// A single instance of this class.
	public static $instance    = null;
	public static $this_plugin = null;
	const APPP_KEY             = 'appbuddy_key';
	const PLUGIN               = 'AppBuddy';
	const VERSION              = '0.9.7';


	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() { $this->init(); }


	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		self::$this_plugin = plugin_basename( __FILE__ );

		// is main plugin active? If not, throw a notice and deactivate
		if ( ! in_array( 'apppresser/apppresser.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'all_admin_notices', array( $this, 'apppresser_required' ) );
			return;
		}

		// is BuddyPress plugin active? If not, throw a notice and deactivate
		if ( ! in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'all_admin_notices', array( $this, 'buddypress_required' ) );
			return;
		}

		// Load translations
		load_plugin_textdomain( 'appbuddy', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Define plugin constants
		$this->plugin['file'] = plugin_basename( __FILE__ );
		$this->plugin['url']  = trailingslashit( plugins_url( '' , __FILE__ ) );
		$this->plugin['dir']  = trailingslashit( plugin_dir_path( __FILE__ ) );

		// Enqueue scripts & styles
		//add_action( 'wp_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_action( 'plugins_loaded', array( $this, 'includes' ) );
		add_action( 'bp_include', array( $this, 'bp_includes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );
	}


	/**
	 * includes function.
	 *
	 * check if in app or admin and include needed files
	 *
	 * @access public
	 * @return void
	 */
	public function includes() {

		include $this->plugin['dir'] . 'inc/AppBuddy_Customizer.php' ;

		if( is_admin() ) {
			include $this->plugin['dir'] . 'inc/admin/admin.php' ;
		}

		appp_updater_add( __FILE__, self::APPP_KEY, array(
			'item_name' => self::PLUGIN, // must match the extension name on the site
			'version'   => self::VERSION,
		) );
	}


	/**
	 * bp_includes function.
	 *
	 * BuddyPress code needs to be loaded on bp_inlude hook
	 *
	 * @access public
	 * @return void
	 */
	public function bp_includes() {
		if( AppPresser::is_app() ) {
			include $this->plugin['dir'] . 'inc/AppBuddy_Modal_Buttons.php' ;
			include $this->plugin['dir'] . 'inc/AppBuddy_Blogs.php' ;
			include $this->plugin['dir'] . 'inc/AppBuddy_Template_Stack.php' ;
			include $this->plugin['dir'] . 'inc/AppBuddy_Ajax.php' ;
		}
		include $this->plugin['dir'] . 'inc/AppBuddy_Notifications.php' ;

	}


	/**
	 * apppresser_required function.
	 *
	 * deactivate notice if AppPresser is not activated
	 *
	 * @access public
	 * @return void
	 */
	public function apppresser_required() {
		echo '<div id="message" class="error"><p>'. sprintf( __( '%1$s requires the AppPresser Core plugin to be installed/activated. %1$s has been deactivated.', 'appbuddy' ), self::PLUGIN ) .'</p></div>';
		deactivate_plugins( self::$this_plugin, true );
	}


	/**
	 * buddypress_required function.
	 *
	 * deactivate notice if AppPresser is not activated
	 *
	 * @access public
	 * @return void
	 */
	public function buddypress_required() {
		echo '<div id="message" class="error"><p>'. sprintf( __( '%1$s requires the BuddyPress plugin to be installed/activated. %1$s has been deactivated.', 'appbuddy' ), self::PLUGIN ) .'</p></div>';
		deactivate_plugins( self::$this_plugin, true );
	}


	/**
	 * register_script function.
	 *
	 * register scripts for AppBuddy
	 *
	 * @access public
	 * @return void
	 */
	public function register_script() {
		if( AppPresser::is_app() ) {
			wp_enqueue_script( 'appbuddy', $this->plugin['url'] .'inc/js/appbuddy.js', array( 'jquery' ), self::VERSION, true );
		}
	}

}
$GLOBALS['AppBuddy'] = new AppBuddy();



function appbuddy_localize_gettext( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Cancel Friendship Request' :
            $translated_text = __( 'Cancel Request', 'appbuddy' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'appbuddy_localize_gettext', 20, 3 );