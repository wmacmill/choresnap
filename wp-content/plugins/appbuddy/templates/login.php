<?php
/**
 * @package AppBuddy Login/Registration template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />

	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
	</head>

	<body class="login-modal">

		<header id="masthead" class="site-header" role="banner">

			<section class="header-inner">

				<div class="pull-left">
				<?php if ( !bp_is_register_page() ) : ?>
					<a href="<?php echo BP_REGISTER_SLUG; ?>" class="nav-left-btn"><?php _e( 'Register', 'appbuddy' ); ?></a>
				<?php else: ?>
					<a href="/" class="nav-left-btn"><?php _e( 'Login', 'appbuddy' ); ?></a>
				<?php endif; ?>
				</div>

				<div class="site-title-wrap">
					<h1 class="site-title page-title"><?php bloginfo( 'name' ); ?></h1>
				</div><!-- .site-title-wrap -->

				<div class="pull-right">
				<?php if ( !bp_is_register_page() ) : ?>
					<a href="#lost-password" class="io-modal-open nav-left-btn"><?php _e( 'Lost Password', 'appbuddy' ); ?></a>
				<?php endif; ?>
				</div>

			</section><!-- .header-inner -->

		</header><!-- #masthead -->

		<div id="main" class="site-content" role="main">


			<?php if ( bp_is_register_page() ) : ?>

			<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

			<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_registration_disabled' ); ?>

					<p><?php _e( 'User registration is currently not allowed.', 'appbuddy' ); ?></p>

				<?php do_action( 'bp_after_registration_disabled' ); ?>
			<?php endif; // registration-disabled signup setp ?>

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<?php do_action( 'template_notices' ); ?>

				<p><?php _e( 'Registering for this site is easy. Just fill in the fields below, and we\'ll get a new account set up for you in no time.', 'appbuddy' ); ?></p>

				<?php do_action( 'bp_before_account_details_fields' ); ?>

				<div class="register-section" id="basic-details-section">

					<?php /***** Basic Account Details ******/ ?>

					<h4><?php _e( 'Account Details', 'appbuddy' ); ?></h4>

					<label for="signup_username"><?php _e( 'Username', 'appbuddy' ); ?> <?php _e( '(required)', 'appbuddy' ); ?></label>
					<?php do_action( 'bp_signup_username_errors' ); ?>
					<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" />

					<label for="signup_email"><?php _e( 'Email Address', 'appbuddy' ); ?> <?php _e( '(required)', 'appbuddy' ); ?></label>
					<?php do_action( 'bp_signup_email_errors' ); ?>
					<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" />

					<label for="signup_password"><?php _e( 'Choose a Password', 'appbuddy' ); ?> <?php _e( '(required)', 'appbuddy' ); ?></label>
					<?php do_action( 'bp_signup_password_errors' ); ?>
					<input type="password" name="signup_password" id="signup_password" value="" />

					<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'appbuddy' ); ?> <?php _e( '(required)', 'appbuddy' ); ?></label>
					<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
					<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />

					<?php do_action( 'bp_account_details_fields' ); ?>

				</div><!-- #basic-details-section -->

				<?php do_action( 'bp_after_account_details_fields' ); ?>

				<?php /***** Extra Profile Details ******/ ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<?php do_action( 'bp_before_signup_profile_fields' ); ?>

					<div class="register-section" id="profile-details-section">

						<h4><?php _e( 'Profile Details', 'appbuddy' ); ?></h4>

						<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
						<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

						<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

							<div class="editfield">

								<?php
								$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
								$field_type->edit_field_html();

								do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

								if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
									<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
										<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'appbuddy' ), bp_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link"><?php _ex( 'Change', 'Change profile field visibility level', 'appbuddy' ); ?></a>
									</p>

									<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
										<fieldset>
											<legend><?php _e( 'Who can see this field?', 'appbuddy' ) ?></legend>

											<?php bp_profile_visibility_radio_buttons() ?>

										</fieldset>
										<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'appbuddy' ) ?></a>

									</div>
								<?php else : ?>
									<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
										<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'appbuddy' ), bp_get_the_profile_field_visibility_level_label() ) ?>
									</p>
								<?php endif ?>

								<?php do_action( 'bp_custom_profile_edit_fields' ); ?>

								<p class="description"><?php bp_the_profile_field_description(); ?></p>

							</div>

						<?php endwhile; ?>

						<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

						<?php endwhile; endif; endif; ?>

						<?php do_action( 'bp_signup_profile_fields' ); ?>

					</div><!-- #profile-details-section -->

					<?php do_action( 'bp_after_signup_profile_fields' ); ?>

				<?php endif; ?>

				<?php if ( bp_get_blog_signup_allowed() ) : ?>

					<?php do_action( 'bp_before_blog_details_fields' ); ?>

					<?php /***** Blog Creation Details ******/ ?>

					<div class="register-section" id="blog-details-section">

						<h4><?php _e( 'Blog Details', 'appbuddy' ); ?></h4>

						<p><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'appbuddy' ); ?></p>

						<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

							<label for="signup_blog_url"><?php _e( 'Blog URL', 'appbuddy' ); ?> <?php _e( '(required)', 'appbuddy' ); ?></label>
							<?php do_action( 'bp_signup_blog_url_errors' ); ?>

							<?php if ( is_subdomain_install() ) : ?>
								http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_blogs_subdomain_base(); ?>
							<?php else : ?>
								<?php echo home_url( '/' ); ?> <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
							<?php endif; ?>

							<label for="signup_blog_title"><?php _e( 'Site Title', 'appbuddy' ); ?> <?php _e( '(required)', 'appbuddy' ); ?></label>
							<?php do_action( 'bp_signup_blog_title_errors' ); ?>
							<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

							<span class="label"><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'appbuddy' ); ?>:</span>
							<?php do_action( 'bp_signup_blog_privacy_errors' ); ?>

							<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'appbuddy' ); ?></label>
							<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'appbuddy' ); ?></label>

							<?php do_action( 'bp_blog_details_fields' ); ?>

						</div>

					</div><!-- #blog-details-section -->

					<?php do_action( 'bp_after_blog_details_fields' ); ?>

				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ); ?>

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( 'Sign Up', 'appbuddy' ); ?>" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

				<?php wp_nonce_field( 'bp_new_signup' ); ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_registration_confirmed' ); ?>

				<?php if ( bp_registration_needs_activation() ) : ?>
					<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'appbuddy' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'appbuddy' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'bp_after_registration_confirmed' ); ?>

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ); ?>

			</form>


			<?php else: ?>

			<?php if( isset( $_REQUEST['error'] ) && $_REQUEST['error'] == 'login_failed' ) : ?>

				<div class="login-modal-message error">
					<p><?php _e('Username or password incorrect.'); ?></p>
				</div>

			<?php endif; ?>

			<?php if( get_theme_mod( 'ab_text_mod' ) != '') : ?>
					<div class="login-modal-text">
						<p><?php echo get_theme_mod( 'ab_text_mod'); ?></p>
					</div>

			<?php endif; ?>

			<?php do_action( 'appbuddy_before_loginform' ); ?>

			<form autocomplete = "off" autocapitalize="off" name="appbuddy-loginform" id="appbuddy-loginform" method="post">
				<p class="status"></p>

				<p><input type="text" name="username" id="username" class="input" placeholder="<?php esc_attr_e( 'Username', 'appbuddy' ); ?>" value="" size="20" /></p>
				<p><input type="password" name="password" id="password" class="input" placeholder="<?php esc_attr_e( 'Password', 'appbuddy' ); ?>" value="" size="20" /></p>

				<input name="rememberme" type="hidden" id="rememberme" value="forever" />
				<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
				<p class="login-submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e( 'Log In', 'appbuddy' ); ?>" />
				</p>

			<?php do_action( 'appbuddy_after_loginform' ); ?>

			</form>

			<?php endif; ?>


		</div><!-- #main -->



		<?php wp_footer(); ?>
	</body>
</html>
