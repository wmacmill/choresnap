<?php
	
/* 
 * Enqueue custom js
 */

function appp_custom_scripts() {
	wp_enqueue_script( 'appp-custom-scripts', get_stylesheet_directory_uri() . '/js/app-custom.js', array('jquery'), '1.0.8', true );
}

add_action( 'wp_enqueue_scripts', 'appp_custom_scripts' );

/* 
 * Show login button in toolbar if user not logged in. AppBuddy only
 */

function my_appbuddy_modal_btn( $button ) {
	
	$args = '';

	if ( is_user_logged_in() && bp_is_current_component('activity') || is_user_logged_in() && bp_is_group_home() ) {

			$args = array(
				'button_class' => 'nav-right-btn io-modal-open',
				'icon_class'   => 'fa fa-lg fa-edit',
				'button_text'  => '',
				'url' => '#activity-post-form'
			);

		} else if ( !is_user_logged_in() ) {

			$args = array(
				'button_class' => 'nav-right-btn login',
				'icon_class'   => 'fa fa-lg fa-sign-in',
				'button_text'  => '',
				'post_in' => '',
				'url' => '#loginModal'
			);

		}
		
		if( isset($args['url'] ) ) {
			$button = sprintf( '<a class="%s io-modal-open" href="%s" data-post="%s"><i class="%s"></i> %s</a>', $args['button_class'], $args['url'], $args['post_in'], $args['icon_class'], $args['button_text'] );
		}

	return $button;
}

add_filter('appbuddy_modal_button', 'my_appbuddy_modal_btn' );

/* 
 * Set cookie on first visit 
 */

function app_is_first_time() {
	
/*
	if( !AppPresser::is_app() ) 
		return;
*/
	
    if ( isset($_COOKIE['_wp_first_time']) || is_user_logged_in() ) {
        return false;
    } else {
        // expires in 30 days.
        setcookie('_wp_first_time', 1, time() + (WEEK_IN_SECONDS * 4), COOKIEPATH, COOKIE_DOMAIN, false);

        return true;
    }
}

add_action( 'init', 'app_is_first_time' );

/* 
 * Show intro screen if it's a first time visit, or user is not logged in
 */
 
function app_show_intro() {
	
	$path=$_SERVER['REQUEST_URI'];	
	
	if( strpos($path, 'intro') == true || is_user_logged_in() || isset( $_COOKIE['_wp_first_time'] ) )
		return;
		
	wp_redirect( 'http://app.reactordev.com/intro' ); 
	exit;
}

add_action( 'init', 'app_show_intro', 999 );