<?php

// only run these functions in app mode
if( AppPresser::is_app() ) {
	add_action( 'bp_register_theme_packages', 'appbuddy_templatepack' );
	add_filter( 'pre_option__bp_theme_package_id', 'appbuddy_templatepack_package_id' );
	if( !defined('APPP_REMOVE_LOGIN') ) {
		add_filter('template_redirect', 'appbuddy_login_screen');
	}
	add_filter('login_redirect', 'appbuddy_login_error', 10, 3);
	add_filter( 'bp_get_activity_action', 'appbuddy_add_target_blank', 10, 2 );
	add_action('wp_footer', 'appbuddy_post_modal_template');
	add_action('wp_footer', 'appbuddy_lost_password_modal_template');
	//add_action( 'bp_setup_nav', 'appbuddy_remove_profile_tabs' );
}


/**
 * appbuddy_templatepack function.
 *
 * @access public
 * @return void
 */
function appbuddy_templatepack() {
	global $AppBuddy;

	bp_register_theme_package( array(
			'id'      => 'templates',
			'name'    => __( 'AppBuddy Templates', 'appbuddy' ),
			'version' => $AppBuddy::VERSION,
			'dir'     => $AppBuddy->plugin['dir'] . 'templates',
			'url'     => $AppBuddy->plugin['url'] . 'templates',
		) );

}


/**
 * appbuddy_templatepack_package_id function.
 *
 * @access public
 * @param mixed $package_id
 * @return void
 */
function appbuddy_templatepack_package_id( $package_id ) {
	return 'templates';
}


/**
 * appbuddy_login_screen function.
 *
 * filters teemplate to a splash screen when logged out
 *
 * @access public
 * @return void
 */
function appbuddy_login_screen() {
	global $AppBuddy;

	if( !is_user_logged_in() && AppPresser::is_app() ) {
		add_filter( 'show_admin_bar', '__return_false' );
		include( $AppBuddy->plugin['dir'] . 'templates/login.php' );
		die();

	}

}


/**
 * appbuddy_login_error function.
 *
 * @access public
 * @param mixed $redirto
 * @param mixed $dirfrom
 * @param mixed $wp_user
 * @return void
 */
function appbuddy_login_error( $redirto, $dirfrom, $wp_user ) {


	if( !is_wp_error( $wp_user ) || !$wp_user->get_error_code() || !AppPresser::is_app() ) return $redirto;

		switch( $wp_user->get_error_code() ) {

			case 'incorrect_password':
			case 'empty_password':
			case 'invalid_username':
			default:
			wp_redirect('/?errors=login_failed');
			exit;

	}

	return $redirto;
}

function filter_ms_blog_url_blank() {

}



/**
 * appbuddy_add_target_blank function.
 *
 * this adds target _blank to MS blog links to open inappbrowser
 *
 * @access public
 * @param mixed $action
 * @param mixed $activity
 * @return void
 */
function appbuddy_add_target_blank( $action, $activity ) {

	if( ! is_multisite() ) return $action;

	switch ( $activity->component ) {
		case 'blogs' :

			$pos = strpos( $action,'<a' );
			if ($pos !== false) {
			    $action = substr( $action, 0, $pos + 1 ) . str_replace( '<a', '<a target="_blank"', substr( $action, $pos + 1 ) );
			}

		break;
	}

	return $action;
}


/**
 * appbuddy_remove_profile_tabs function.
 *
 * @access public
 * @return void
 */
function appbuddy_remove_profile_tabs() {
	global $bp;
	$bp->bp_nav['groups'] = false;
	$bp->bp_nav['friends'] = false;
	$bp->bp_nav['blogs'] = false;

	$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites'] = false;
}


function appbuddy_post_modal_template() {
	?>
	<aside class="io-modal" id="activity-post-form" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="toolbar site-header">
			<i class="io-modal-close fa fa-times fa-lg alignright"></i>
		</div>
		<div class="io-modal-content">
		<?php if ( is_user_logged_in() ) : ?>
			<?php bp_get_template_part( 'activity/post-form' ); ?>
		<?php endif; ?>
		</div>
	</aside>
		<?php
}
	
	/* Deprecated: moved to core plugin + apptheme */
	function appbuddy_lost_password_modal_template() {
		if( !is_user_logged_in() ) {
		?>
	<aside class="io-modal" id="lost-password" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="toolbar site-header">
			<i class="io-modal-close fa fa-times fa-lg alignright"></i>
		</div>
		<div class="io-modal-content">
			<p><?php _e( 'Please enter your email and a password retrieval code will be sent.', 'appbuddy' ) ?></p>
			<p><input type="text" id="email" name="email" value="" placeholder="<?php _e( 'Email', 'appbuddy' ); ?>"/></p>
			<button type="button" id="new-password" class="button btn-primary"><?php _e( 'Request Code', 'appbuddy' )?></button>
			<?php wp_nonce_field( 'new_password','get_new_password' ); ?>
			<span class="code-rsp"></span>

			<br/><br/>

			<h4><?php _e('New Password', 'appbuddy' )?></h4>

			<p><?php _e('Please enter your code and a new password.', 'appbuddy' ) ?></p>
			<p><input type="text" id="code" name="code" value="" placeholder="<?php _e( 'Code', 'appbuddy' ); ?>"/></p>
			<p><input type="password" id="pw" name="pw" value="" placeholder="<?php _e( 'New Password', 'appbuddy' ); ?>"/></p>
			<p><input type="password" id="pwr" name="pwr" value="" placeholder="<?php _e( 'Repeat Password', 'appbuddy' ); ?>"/></p>
			<button type="button" id="change-password" class="button btn-primary"><?php _e('Change Password', 'appbuddy' )?></button>
			<span class="psw-rsp"></span>

			</div>

		<script>
		jQuery('body').on('click', '#new-password', function() {

			if ( jQuery('#email').val() === '' ) {
				jQuery('.code-rsp').html('<?php _e( 'Email required.', 'appbuddy' ); ?>');
				return false;
			}

			jQuery('.code-rsp').html('<i class="fa fa-cog fa-spin"></1>');

			var data = {
		  		action: 'lost_password',
		  		email: jQuery('#email').val(),
		  		nonce: jQuery('#get_new_password').val()
		  	};

		  	var reset = jQuery.ajax({
					type: 'post',
					url : ajaxurl,
					dataType: 'json',
					data : data,
					success: function( response ) {
						jQuery('.code-rsp').html(response.data.message);
						jQuery('input[type=text]').val('');
						jQuery('input[type=password]').val('');
					}

			});

			return reset;

		});

		jQuery('body').on('click', '#change-password', function() {

			if ( jQuery('#pw').val() != jQuery('#pwr').val() || jQuery('#pw').val() === '' ) {
					jQuery('.psw-rsp').html('<?php _e( 'Passwords do not match.', 'appbuddy' ); ?>');
					return false;
			}

			if ( jQuery('#code').val() === '' ) {
					jQuery('.psw-rsp').html('<?php _e( 'Please enter your reset code.', 'appbuddy' ); ?>');
					return false;
			}

			jQuery('.psw-rsp').html('<i class="fa fa-cog fa-spin"></1>');

			var data = {
		  		action: 'validate_password',
		  		code: jQuery('#code').val(),
		  		password: jQuery('#pw').val(),
		  		nonce: jQuery('#get_new_password').val()
		  	};

		  	var validation = jQuery.ajax({
					type: 'post',
					url : ajaxurl,
					dataType: 'json',
					data : data,
					success: function( response ) {
						jQuery('.psw-rsp').html(response.data.message);
						jQuery('input[type=text]').val('');
						jQuery('input[type=password]').val('');
						if( response.message ) {
							window.location.reload();
						}
					}

			});

			return validation;

		});

		</script>
	</aside>
	<?php
	}
}


function bp_the_thread_message_sender_class() {
	echo bp_get_the_thread_message_sender_class();
}

function bp_get_the_thread_message_sender_class() {
	global $thread_template;

	$user_id = bp_displayed_user_id();

	if( $thread_template->message->sender_id == $user_id ) return 'flip';

}



function remove_woo_body_class($wp_classes) {

	if( bp_current_component() ) :
	      foreach($wp_classes as $key => $value) {
	      if ($value == 'woocommerce') unset($wp_classes[$key]);
	      }
	endif;

	return $wp_classes;
}
add_filter('body_class', 'remove_woo_body_class', 20, 2);