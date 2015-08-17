<div id="activity-form-modal">
		<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form-in-modal" name="whats-new-form" role="complementary">

			<div id="whats-new-avatar">
				<a href="<?php echo bp_loggedin_user_domain(); ?>">
					<?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
				</a>
			</div>

			<p class="activity-greeting"><?php if ( bp_is_group() )
				printf( __( "What's new in %s, %s?", 'appbuddy' ), bp_get_group_name(), bp_get_user_firstname() );
			else
				printf( __( "What's new, %s?", 'appbuddy' ), bp_get_user_firstname() );
			?></p>

			<div id="whats-new-content">
				<div id="whats-new-textarea">
					<textarea name="whats-new" id="whats-new" cols="50" rows="10"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?> <?php endif; ?></textarea>
				</div>

				<div id="whats-new-options">
					<div id="whats-new-submit">

					<button type="button" name="aw-whats-new-submit" id="aw-whats-new-submit" class="btn btn-primary btn-camera"><i class="fa fa-pencil"></i> <?php esc_attr_e( ' Post Update', 'appbuddy' ); ?></button>

					</div>

				</div><!-- #whats-new-options -->
			</div><!-- #whats-new-content -->

			<?php if ( bp_is_group_home() ) : ?>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />

			<?php endif; ?>
			<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
			<?php do_action( 'appbuddy_after_activity_post_form' ); ?>

		</form><!-- #whats-new-form -->


		<div id="activity_add_media" class="">
			<?php if(function_exists('appp_camera') && 'on' === appp_get_setting( 'appcam_appbuddy' ) ) : ?>
				<button type="button" id="attach-photo" class="btn btn-primary btn-camera"><i class="fa fa-camera"></i> <?php _e('Attach Photo', 'appbudy'); ?></button>
				<div id="image-status"></div>
			<?php endif; ?>
		</div>
</div>

	<div id="attach-image-sheet" class="action-sheet-backdrop hide appbuddy">
		<div class="action-sheet-wrapper action-sheet-up">
			<div class="action-sheet">
				<div class="action-sheet-title"><?php _e( 'Choose an Image', 'appbuddy' ); ?></div>
				<div class="action-sheet-group">
					<?php if( function_exists('appp_camera') ) : ?>
					<?php appp_camera( array('action' => 'appbuddy', 'description' => '') ); ?>
					<?php endif; ?>
				</div>
				<div class="action-sheet-group">
					<button type="button" class="button btn-primary destructive"><?php _e( 'Cancel', 'appbuddy' ); ?></button>
				</div>
			</div>
		</div>
	</div>
