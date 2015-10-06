<?php
/**
 * Jobify Child Theme
 *
 * Place any custom functionality/code snippets here.
 *
 * @since Jobify Child 1.0.0
 */


function jobify_child_styles() {
    wp_enqueue_style( 'jobify-child', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'jobify_child_styles', 20 );

/*
* This modifies the capabilities that are required to force a strong password https://github.com/boogah/Force-Strong-Passwords
* This currently only requires update_core (admin) & 
*/
 add_filter( 'slt_fsp_caps_check', 'my_caps_check' );
    function my_caps_check( $caps ) {
        unset ($caps);
        $caps[] = 'update_core';
        return $caps;
}

//* Enqueue styles for dashicons on front end
add_action( 'wp_enqueue_scripts', 'themeprefix_enqueue_styles' );
function themeprefix_enqueue_styles() {
  wp_enqueue_style( 'dashicons' );
}

/*
*
*General Wordpress Snippets
*
*/

//sets the upload limit for non admin users to 5mb
add_filter( 'upload_size_limit', 'limit_upload_size_limit_for_non_admin' );

function limit_upload_size_limit_for_non_admin( $limit ) {
  if ( ! current_user_can( 'manage_options' ) ) {
    $limit = '5000000'; // 5mb in bytes
  }
  return $limit;
}

/*enables shortcodes in text widget*/
add_filter('widget_text', 'do_shortcode');



/*
*
* Trying to add meta updates to the credit meta. Goes through the post & checks for the category - updates cred field with the number
*
*/
function update_credit_meta ( ) {
  $post_ID = get_the_ID();
  $cleaning_frequency = get_post_meta( $post_ID, '_chore_cleaning_frequency', true ); //check the frequency - used a lot in cleaning checking
  
  if ( 'job_listing' != get_post_type ( $post_ID ) ) {
    return;
  }

  else { //go through each category, set credit amount, updates at the end
    if( has_term ( 'Apartment Cleaning', 'job_listing_category', $post_ID ) ) {
      if ( $cleaning_frequency != 'One time' ) {
        $credit_amount = 3;
      }
      else {
        $credit_amount = 2;
      }
    } 
    elseif( has_term ( 'Condo Cleaning', 'job_listing_category', $post_ID ) ) {
      if ( $cleaning_frequency != 'One time' ) {
        $credit_amount = 3;
      }
      else {
        $credit_amount = 2;
      }
    }
    elseif( has_term ( 'House Cleaning', 'job_listing_category', $post_ID ) ) {
     if ( $cleaning_frequency != 'One time' ) {
        $credit_amount = 5;
      }
      else {
        $credit_amount = 3;
      } 
    }
    else $credit_amount = 2;//this defaults it to 2
  }

  update_post_meta ( $post_ID, '_cred_field', $credit_amount );

}

add_action ( 'single_job_listing_meta_end', 'update_credit_meta');


/****************************** ADMIN BAR FUNCTIONS ******************************/

//woocommerce disables the admin bar by default
if ( is_user_logged_in () ) {
  add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
}

/**
 * Remove certain admin bar links
 *
 * @since BuddyBoss 2.1
 */
function remove_admin_bar_links() {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('wp-logo');
  $wp_admin_bar->remove_node('my-account-messages-compose');//removes the compose menu from messages in buddypress
  $wp_admin_bar->remove_node('my-account-xprofile-public');
  $wp_admin_bar->remove_node('my-account-settings-general');
  $wp_admin_bar->remove_node('my-account-settings-profile');

  if (!current_user_can('administrator')):
    $wp_admin_bar->remove_menu('site-name');
  endif;

  if (!current_user_can('candidate')):
    $wp_admin_bar->remove_menu('mycred-account');
  endif;

}
add_action( 'admin_bar_menu', 'remove_admin_bar_links', 999 ); //wp_before_admin_bar_render


/**
 * Replace admin bar "Howdy" text
 *
 * @since BuddyBoss 2.1.1
 */
function replace_howdy( $wp_admin_bar ) {

  if ( is_user_logged_in() ) {

      $my_account=$wp_admin_bar->get_node('my-account');
      $newtitle = str_replace( 'Howdy,', '', $my_account->title );
      $wp_admin_bar->add_node( array(
          'id' => 'my-account',
          'title' => $newtitle,
      ) );

  }
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );

function disable_bar_search() {  
    global $wp_admin_bar;  
    $wp_admin_bar->remove_menu('search');  
}  
add_action( 'wp_before_admin_bar_render', 'disable_bar_search' );  

/*adding the styling for the buddypress admin bar*/

function fb_move_admin_bar() {
  if ( !current_user_can ('manage_options')) {
    echo '
    <style type="text/css">
    body.admin-bar {
        margin-top: -32px;
        padding-bottom: 32px;
    }
    #wpadminbar {
      background: #3396d1 none repeat scroll 0 0;
    }
    #wp-admin-bar-notes {
      display: none;
    }
    
    .copyright {
      padding: 50px !important;
    }
    @media screen and ( max-width: 782px ) {
        #wpadminbar {
          position: fixed;
        }
    }

    @media screen and ( max-width: 1200px ) {
        body.admin-bar {
          margin-top: -46px;
          padding-bottom: 46px;
        }
        #wpadminbar .ab-top-menu>.menupop>.ab-sub-wrapper {
          bottom:46px;
        }
        #wpadminbar {
        top: auto !important;
        bottom: 0;
        }
        #wpadminbar .quicklinks>ul>li {
            position:relative;
        }
        #wpadminbar .ab-top-menu>.menupop>.ab-sub-wrapper {
            bottom:32px;
        }
    }    
    </style>';
    if (is_page(1982)) {//removes the background on the admin bar on the homepage
      echo '
      <style type="text/css">
        #wpadminbar {
          background: none !important;
        }
      </style>';
    }
  } 
}

// remove the following line if you want to keep the admin bar at the top on the backend
//add_action( 'admin_head', 'fb_move_admin_bar' );
// remove the following line if you want to keep the admin bar at the top on the frontend
add_action( 'wp_head', 'fb_move_admin_bar' );
/************end admin bar styling ************************************/


/************************testing populating the taxonomy field**********************************/
/*add_filter( 'gform_pre_render', 'populate_cities' );
add_filter( 'gform_pre_validation', 'populate_cities' );
add_filter( 'gform_pre_submission_filter', 'populate_cities' );
add_filter( 'gform_admin_pre_render', 'populate_cities' );
function populate_cities( $form, $province ) {

    foreach ( $form['fields'] as &$field ) {

        if ( $field->type != 'post_custom_field' || strpos( $field->cssClass, 'populate-posts' ) === false ) {
            continue;
        }

        $post_id = 119;
        $chore_taxonomy = 'job_listing_region';
        $posts = get_term_children( $post_id, $chore_taxonomy );//get_users ( $args );

        $choices = array();

        foreach ( $posts as $post ) {
            $term = get_term_by( 'id', $post, $chore_taxonomy );
            $choices[] = array( 'text' => 'Ontario => ' . $term->name, 'value' => 'id' );
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a City';
        $field->choices = $choices;

    }

    return $form;
}


add_filter( 'gform_pre_render', 'populate_provinces' );
add_filter( 'gform_pre_validation', 'populate_provinces' );
add_filter( 'gform_pre_submission_filter', 'populate_provinces' );
add_filter( 'gform_admin_pre_render', 'populate_provinces' );
function populate_provinces ( $form ) {
      
     foreach ( $form['fields'] as &$field ) {

      if ( $field->type != 'post_custom_field' || strpos( $field->cssClass, 'populate-provinces' ) === false ) {
            continue;
        }
      
        //$post_id = 119;
        $chore_taxonomy = 'job_listing_region';
        $posts = get_terms ( $chore_taxonomy, array ( 
                                      'orderby' => 'name',
                                      'order' => 'asc',
                                      'parent' => 0,
                                      'hide_empty' => 0,
                                      'fields' => 'all'
                                       ) );

        $choices = array();

        foreach ( $posts as $post ) {
            $city_parent_id = $post->parent;
            $city_parent = get_term ( $city_parent_id, 'job_listing_region' );
            $city_parent_name = $city_parent->name;
            $choices[] = array( 'text' => $city_parent_name . '-' . $post->name, 'value' => $post->term_id );
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a Province';
        $field->choices = $choices;

    }

    return $form;
      
}*/

/**working on cmb2 stuff here*/

/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

add_action( 'cmb2_init', 'yourprefix_register_demo_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_init' hook.
 */
function yourprefix_register_demo_metabox() {

  // Start with an underscore to hide fields from custom fields list
  $prefix = '_yourprefix_demo_';

  /**
   * Sample metabox to demonstrate each field type included
   */
  $cmb_demo = new_cmb2_box( array(
    'id'            => $prefix . 'metabox',
    'title'         => __( 'Gallery Link', 'cmb2' ),
    'object_types'  => array( 'resume', ), // Post type
    // 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
    // 'context'    => 'normal',
    // 'priority'   => 'high',
    // 'show_names' => true, // Show field names on the left
    // 'cmb_styles' => false, // false to disable the CMB stylesheet
    // 'closed'     => true, // true to keep the metabox closed by default
  ) );

  $cmb_demo->add_field( array(
    'name' => 'Gallery Images',
    'desc' => 'Upload and manage gallery images',
    'button' => 'Manage gallery', // Optionally set button label
    'id'   => $prefix . 'gallery_images',
    'type' => 'pw_gallery',
    'sanitization_cb' => 'pw_gallery_field_sanitise',
  ) );

 }

/**end CMB2 plugin test**/

//this works to get the notifications into the db - now how to get them to show on the front end?
/*function will_testing_bp_notifications () {
  if (!is_page(1982)) {
    return;
  }
   //if you visit the homepage then go
  if ( bp_is_active( 'notifications' ) ) {
      bp_notifications_add_notification( array(
          'user_id'           => get_current_user_id(),
          'item_id'           => get_the_ID(),
          //'secondary_item_id' => $activity->user_id,
          'component_name'    => 'test_message',
          'component_action'  => 'new_test_mention',
          'date_notified'     => bp_core_current_time(),
          'is_new'            => 1,
      ) );
  }
}

/*bump this version on git so it is in sync*/


/*adding a new email template to the application process*/
add_action( 'new_job_application', 'applicant_send_application_confirmation', 10, 2 );

function applicant_send_application_confirmation ( $application_id, $job_id ) {
  $candidate_email = get_post_meta( $application_id, '_candidate_email', true );
  $candidate_message         = get_email_body_for_candidate( $application_id, $job_id );
  $headers   = array();
          $headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <noreply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', site_url( '' ) ) . '>';
          $headers[] = 'Content-Type: text/html';
          $headers[] = 'charset=utf-8';

  wp_mail( $candidate_email, 'Your Application on ' . get_bloginfo( 'name' ), $candidate_message, $headers );       
}

function get_email_body_for_candidate( $application_id, $job_id ) {
    $site_url = site_url();
    $body = '
      Congrats on placing an estimate on Chore Snap. For your records here are all the details you included in your bid:<br>
      <br>
      <b>Bid:</b> $' . get_post_meta ($application_id, 'Estimate', true) . '<br>
      <b>Message:</b> ' . get_post_meta($application_id, 'Message', true) . '<br>
      <b>What is included:</b> ' . get_post_meta($application_id, 'What is included?', true) . '<br>
      <br>As always you can manage your online company profile by visiting your online dashboard <a href="' . $site_url . '/my-company/">here</a>.<br>';
    
    return $body;
}

/*
//start by filtering the email to remove it
function filter_bp_messages_email ( $email_to, $ud ) {
    $email_to = '';
    return $email_to;
}
add_filter( 'messages_notification_new_message_to', 'filter_bp_messages_email', 10, 2 );

function bp_custom_messages_email( $recipients, $email_subject, $email_content, $args ) {
    // Create your $headers and $attachments here
     $headers   = array();
          $headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <noreply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', site_url( '' ) ) . '>';
          $headers[] = 'Content-Type: text/html';
          $headers[] = 'charset=utf-8';
    // Send the email.
    wp_mail( $recipients, $email_subject, $email_content, $headers );
}
add_action( 'bp_messages_sent_notification_email', 'bp_custom_messages_email', 10, 5 );
*/

add_filter ( 'messages_notification_new_message_message', 'will_custom_bp_message_notification', 10, 7);

function will_custom_bp_message_notification ( $email_content, $sender_name, $subject, $content, $message_link, $settings_link, $ud ) {
  $message = sprintf( __(
'%1$s sent you a new message:<br>
<br>
Subject: %2$s<br>
<br>
"%3$s"<br>
<br>
To view and read your messages please log in and go to <a style="text-decoration:none; color: #85c0e3; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: text-align: left;" href="%4$s"> My Messages </a>.<br>
<br>
---------------------<br>
', 'buddypress' ), $sender_name, $subject, $content, $message_link );

// Only show the disable notifications line if the settings component is enabled
if ( bp_is_active( 'settings' ) ) {
  $message .= sprintf( __( '<br>To disable these notifications, <a style="text-decoration:none; color: #85c0e3; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: text-align: left;" href="%s">Click Here</a>', 'buddypress' ), $settings_link );
}

  return $message;
}

//adding html to the "password changed email" when a password is changed this is sent to the user confirming the change
add_filter ( 'password_change_email', 'custom_password_change_email', 8, 3);

function custom_password_change_email ( $pass_change_email, $user, $userdata ) {
  $pass_change_email['message'] = _('Hi ' . $user['display_name'] . ',<br>
    <br>
    This notice confirms that your password was changed on ###SITENAME###.<br>
    <br>
    If you did not change your password, please contact the site administrator at
    <a style="text-decoration:none; color: #85c0e3; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: text-align: left;" href="mailto:security@choresnap.com">security@choresnap.com</a><br>
    ' );
  
  return $pass_change_email;
}


/*changes Username to Username & Email on login*/
function wpse60605_change_username_label( $defaults ){
    $defaults['label_username'] = __( 'Username or Email' );
    return $defaults;
}
add_filter( 'login_form_defaults', 'wpse60605_change_username_label' );

?>