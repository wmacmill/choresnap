<?php
/*
Plugin Name: Gravity Forms Geolocation Add-on
Plugin URI: http://www.geomywp.com
Description: Enhance Gravity Forms plugin with geolocation features
Version: 2.0-beta-7
Author: Eyal Fitoussi
Author URI: http://www.geomywp.com
Requires at least: 4.0
Tested up to: 4.2.2
Gravity forms: 1.9+
GEO my WP: 2.6.1+
Text Domain: GFG
Domain Path: /languages/
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

//abort if older version Gravity Forms GEO Field activated
if ( class_exists( 'Gravity_Forms_GEO_Fields' ) ) {
    add_action(
        'admin_notices',
        create_function(
            '', 
            '<div class="error">
                <p>
                    '.__( "Please deactivate Gravity Forms Geo Field add-on in order to use Gravity Forms Geolocation add-on.", "GFG" ).'
                </p>
            </div>'
        )
    );
    return false;
}

//verify that Gravity Forms plugin is activated
if ( class_exists( "GFForms" ) ) {

    /**
     * Include Gravity Forms add-on framework
     */
    GFForms::include_addon_framework();

    /**
     * Gravity Forms Geolocation child class
     *
     * @since 2.0
     * @author Eyal Fitoussi
     */
    class Gravity_Forms_Geolocation extends GFAddOn {

        //pLugin version
        protected $_version = "2.0";

        //required Gravity Forms
        protected $_min_gravityforms_version = "1.9";

        //Plugin's name
        protected $_title = "Gravity Forms Geolocation";

        //plugin's item ID
        protected $_item_id = 2273;

        //License name
        protected $_license_name = 'gravity_forms_geo_fields';

        protected $_slug = "gravityforms_geolocation";
        protected $_path = "gravityforms-geolocation/gravityforms-geolocation.php";
        protected $_full_path = __FILE__;
        protected $_short_title = "Geolocation";
        
        public function init(){
            parent::init();
            
            //load textdomain
            load_plugin_textdomain( 'GFG', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

            //define globals
            define( 'GFG_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( $this->_full_path ) ), basename( $this->_full_path ) ) ) );
            define( 'GFG_PATH', untrailingslashit( plugin_dir_path( $this->_full_path ) ) );

            if ( !defined( 'GMW_REMOTE_SITE_URL' ) ) {
                define( 'GMW_REMOTE_SITE_URL', 'https://geomywp.com' );
            }     
        }

        /**
         * Admin init
         * @return void
         */
        public function init_admin(){
            parent::init_admin();
            
            //add addon to GEO my WP add-ons page
            if ( class_exists( 'GEO_my_WP') ) {
                add_filter( 'gmw_admin_addons_page', array( $this, 'gmw_addon_init' ) );
            } else {

                //include plugin updater files
                include_once( 'updater/geo-my-wp-updater.php' );
                include_once( 'updater/geo-my-wp-license-handler.php' );

                 //add license key box to plugins page
                add_action( 'after_plugin_row_'.plugin_basename( $this->_full_path ), array( $this, 'license_key' ) );    
            }

            //form settings page
            include( 'includes/admin/ggf-admin-form-settings-page.php' ); 
            
            //form editor page
            include( 'includes/admin/ggf-admin-form-editor-page.php' ); 
              
            //if user registration add-on installed and include its file
            if ( class_exists( 'GFUser' ) ) {
                include( 'includes/admin/ggf-admin-user-registration.php' );
            }

            //form submission class
            include( 'includes/ggf-form-submission-class.php' );

            //Check for plugin updates
            if ( class_exists( 'GMW_License' ) ) {
                $ggf_license = new GMW_License( $this->_full_path, $this->_title, $this->_license_name, $this->_version, 'Eyal Fitoussi' );
            }
            
        }

        /**
         * Frontend init
         * @return void 
         */
        public function init_frontend(){
            parent::init_frontend();

            //enqueue Google Maps API
            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );               

            //include frontend classes
            include( 'includes/ggf-form-submission-class.php' );
            include( 'includes/ggf-frontend-form-class.php' );
        }

        /**
         * Include addon function - for GEO my WP
         *
         * @access public
         * @return $addons
         */
        public function gmw_addon_init( $addons ) {

            $addons[$this->_license_name] = array(
                    'name'      => $this->_license_name,
                    'title'     => 'Gravity Forms Geolocation',
                    'version'   => $this->_version,
                    'item'      => $this->_title,
                    'file'      => $this->_full_path,
                    'author'    => 'Eyal Fitoussi',
                    'desc'      => __( 'Enhance Gravity Forms plugin with geolocation features.', 'GFG' ),
                    'license'   => true,
                    'image'     => false,                    
            );
            return $addons;
        }
                
        /**
         * add license key input field in the plugins page
         * 
         * @since 2.5
         * @author Eyal Fitoussi
         */
        public function license_key() {       
            if ( class_exists( 'GMW_License_Key' ) ) {
                $gjm_license_key = new GMW_License_Key( $this->_full_path, $this->_title, $this->_license_name );
                $gjm_license_key->license_key_output();  
            }   
        }

        /**
         * frontend_scripts function.
         *
         * @access public
         * @return void
         */
        public function register_scripts() {

            if ( !class_exists( 'GEO_my_WP' ) || !wp_script_is( 'google_maps', 'registered' ) ) {

                wp_deregister_script( 'google-maps' );
                $protocol  = is_ssl() ? 'https' : 'http';

                //Build Google API url. elements can be modified via filters
                $google_url = apply_filters( 'ggf_google_maps_api_url', array( 
                    'protocol'  => is_ssl() ? 'https' : 'http',
                    'url_base'  => '://maps.googleapis.com/maps/api/js?',
                    'url_data'  => http_build_query( apply_filters( 'ggf_google_maps_api_args', array(
                            'libraries' => 'places',
                            'key'       => '',
                            'region'    => 'us',
                            'language'  => 'en',
                            'sansor'    => 'false'
                    ) ), '', '&amp;'),
                ) );

                wp_register_script( 'google-maps', implode( '', $google_url ) , array( 'jquery' ), $this->_version, false );
                wp_register_script( 'gfg-script', GFG_URL.'/assets/js/gfg.min.js', array( 'jquery', 'google-maps' ), false, true );
            }          
        }

        /**
         * Register/enqueue styles
         * @return void
         * @since 2.0
         */
        public function styles() {
            $styles = array(
                array(
                    "handle"  => "gfg_style_admin",
                    "src"     => $this->get_base_url() . "/updater/assets/css/style-admin.css",
                    "version" => $this->_version,
                    "enqueue" => array(
                            array( "query" => "plugins.php" )
                    )
                )
            );
            return array_merge(parent::styles(), $styles);
        }
    }
    new Gravity_Forms_Geolocation();
}