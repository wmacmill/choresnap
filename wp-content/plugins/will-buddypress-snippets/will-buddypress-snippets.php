<?php
/*
Plugin Name: Will - Buddypress Snippets
Plugin URI:
Description: This is a site specific plugin that's used to store the snippets to modify the core Buddypress functionality or edit the add on functionality
Version: 0.1
Author: Will MacMillan
Author URI: http://www.facebook.com/macmillan.will
Text Domain: 
Domain Path: 
*/
/* Start Adding Functions Below this Line */


/*testing adding private messaging to the applications page*/
/* see url http://buddydev.com/buddypress/add-send-private-message-button-in-members-directory-on-a-buddypress-network/ */
/**
 * Get the User Id in the current context
 * @param int $user_id
 * @return int user_id
 */
function hibuddy_get_context_user_id( $application, $user_id=false ){
 
if ( bp_is_my_profile() || !is_user_logged_in() )
 return false;
 if( !$user_id )
 $user_id = $application;//get_post_meta ( $application , '_job_application_author', true);//get_post_meta($application->ID, '_job_application_author', true);
 
 return apply_filters( 'hibuddy_get_context_user_id', $user_id );
}

function hibuddy_get_send_private_message_url( $application ) {
 
$user_id = hibuddy_get_context_user_id( $application );
 
if( !$user_id || $user_id == bp_loggedin_user_id() )
 return;
 
 if ( bp_is_my_profile() || !is_user_logged_in() )
 return false;
 
return apply_filters( 'hibuddy_get_send_private_message_url', wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) ) );
}

function hibuddy_get_send_private_message_button( $application ) {
 //get the user id to whom we are sending the message
 $user_id = hibuddy_get_context_user_id( $application );
 
 //don't show the button if the user id is not present or the user id is same as logged in user id
 if( !$user_id || $user_id == bp_loggedin_user_id() )
 return;
$defaults = array(
 'id' => 'private_message-'.$user_id,
 'component' => 'messages',
 'must_be_logged_in' => true,
 'block_self' => true,
 'wrapper_id' => 'send-private-message-'.$user_id,
 'wrapper_class' =>'send-private-message',
 'link_href' => hibuddy_get_send_private_message_url( $application ),
 'link_title' => __( 'Send a private message to this user.', 'buddypress' ),
 'link_text' => __( 'Private Message', 'buddypress' ),
 'link_class' => 'send-message',
 );
 
 $btn = bp_get_button( $defaults );
 
 return apply_filters( 'hibuddy_get_send_private_message_button', $btn );
}

function hibuddy_send_private_message_button( $application ) {
 if ( bp_is_active ( 'messages') )//if messages not activated in buddypress do nothing
  echo hibuddy_get_send_private_message_button( $application );
}

add_filter ( 'bp_send_private_message', 'hibuddy_send_private_message_button' );
/*ends the function for adding private messaging button*/


/************ customizing the buddypress menus (ends up being messages page) ************/

//removes some tabs from the buddypress profile page
function bp_remove_nav_tabs() {
//bp_core_remove_nav_item( 'profile' );
//bp_core_remove_nav_item ( 'settings' );
bp_core_remove_subnav_item( 'settings', 'profile' );
bp_core_remove_subnav_item ( 'profile', 'view');
}
add_action( 'bp_setup_nav', 'bp_remove_nav_tabs', 15 );


/*this adds a new settings button so that it will default to the notifications section - original is hidden with css*/
function bp_move_messages_menu_order () {
  global $bp;
    // Change the order of menu items
    $bp->bp_nav['messages']['position'] = 1;
}
add_action( 'bp_setup_nav', 'bp_move_messages_menu_order', 999 );

function bp_new_settings_menu () {
  global $bp;

    bp_core_new_nav_item( array( 
            'name' => __( 'Settings', 'buddypress' ), 
            'slug' => 'settings/notifications',//$bp->settings->slug, 'notifications', 
            'position' => 100,
            'show_for_displayed_user' => false, 
            //'screen_function' => 'my_new_settings_template',
            'default_subnav_slug' => 'notifications', 
      ) );
}
add_action( 'bp_setup_nav', 'bp_new_settings_menu' );


/************ends bp menu customization ******************/

/*********other bp stuff *****************/
//sets the default buddypress tab as messages
define( 'BP_DEFAULT_COMPONENT', 'messages' );

define( 'BP_SETTINGS_DEFAULT_EXTENSION', 'notifications' );

//makes the buddypress profiles root instead of including "members"
define ( 'BP_ENABLE_ROOT_PROFILES', true );

define ( 'BP_MESSAGES_SLUG', 'messages' );

define( 'BP_PROFILE_DEFAULT_EXTENSION', 'edit' );

/**************end other buddypress stuff *************/

//can't get styles.css to load these for some reason so just making a function to do it
function will_buddypress_message_menus () {
  
  if ( !(bp_is_messages_component() || bp_is_settings_component() || bp_is_notifications_component() || bp_is_profile_component() ) )
    return;
  
  echo '
   <style type="text/css">
   #buddypress #object-nav li {
      border: 1px solid;
      border-radius: 5px;
      margin: 5px;
      border-color: #3396d1;
    }
    #buddypress #object-nav li > a:hover{
      background-color: #3396d1;
      color: #fff;
    }
    #settings-personal-li {
      display: none !important;
    }
    #compose-personal-li {
      display: none;
    }
    #buddypress #subnav li {
      border-style: solid;
      border-width: 1px;
      margin: 5px !important;
      border-radius: 5px;
      border-color: #3396d1;
    }
    #buddypress #subnav li > a:hover {
      background-color: #3396d1;
      color: #fff;
    }
    #buddypress div.item-list-tabs ul li.current a, #buddypress div.item-list-tabs ul li.selected a {
      background-color: #3396d1;
      color: #fff;
      font-weight: 700;
      opacity: 0.8;
    }
    #buddypress #subnav #members-order-select {
      border: medium none;
    }
    #subnav #general-personal-li, #subnav #notifications-personal-li {
      display: none;
    }
    #buddypress #messages_search_submit {
      margin-top: 5px;
    }
    #buddypress .thread-options > a, #buddypress .notification-actions > a {
      border: 1px solid;
      border-radius: 5px;
      margin: 5px;
      padding: 4px;
    }
    #buddypress .thread-options > a:hover, #buddypress .notification-actions > a:hover {
      background-color: #3396d1;
      color: #fff;
      text-decoration: none;
    }
    #buddypress .thread-checkbox .avatar {
      display: none;
    }
    #buddypress #message-threads thead th, #buddypress .notifications thead th {
      background: #3396d1 none repeat scroll 0 0;
      color: #fff;
    }
    .button.action {
      margin-top: 5px;
      border-radius: 5px;
      border-color: #3396d1;
    }
    #buddypress #public-personal-li {
      display: none;
    }
    </style>';
}

add_action ( 'wp_head', 'will_buddypress_message_menus' );

function bp_adding_notification_icon () {
  echo '
   <style type="text/css">
    @media screen and ( min-width: 1200px ) {
      #wp-admin-bar-bp-notifications .ab-item::before {
        color: yellow;
        content: "";
        margin-right: -8px;
        margin-top: 5px;
        z-index: -1;
      }
      .ab-sub-wrapper #wp-admin-bar-bp-notifications-default .ab-item:before {
        content: none !important;
      }
    }
   </style>';
}
add_action ('wp_footer', 'bp_adding_notification_icon', 999);

?>