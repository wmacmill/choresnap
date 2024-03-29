<form action="<?php bp_messages_form_action('compose' ); ?>" method="post" id="send_message_form" class="standard-form" role="main" enctype="multipart/form-data">

	<?php do_action( 'bp_before_messages_compose_content' ); ?>

	<label for="send-to-input"><?php _e("Send To", 'buddyboss' ); ?></label>
	<ul class="first acfb-holder">
		<li>
			<?php bp_message_get_recipient_tabs(); ?>
		</li>
	</ul>

	<?php if ( bp_current_user_can( 'bp_moderate' ) ) : ?>
		<label for="send-notice" class="send-notice-label"><span class="btn"><input type="checkbox" id="send-notice" name="send-notice" value="1" /> </span><span class="text"><?php _e( "This is a notice to all users.", "buddyboss" ); ?></span></label> 
	<?php endif; ?>

	<label for="subject"><?php _e( 'Subject', 'buddyboss' ); ?></label>
	<input type="text" name="subject" id="subject" value="<?php bp_messages_subject_value(); ?>" />

	<label for="content"><?php _e( 'Message', 'buddyboss' ); ?></label>
	<textarea name="content" id="message_content" rows="15" cols="40"><?php bp_messages_content_value(); ?></textarea>

	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames(); ?>" />

	<?php do_action( 'bp_after_messages_compose_content' ); ?>

	<div class="submit">
		<input type="submit" value="<?php _e( "Send Message", 'buddyboss' ); ?>" name="send" id="send" />
	</div>

	<?php wp_nonce_field( 'messages_send_message' ); ?>
</form>

<script type="text/javascript">
	document.getElementById("send-to-input").focus();
</script>

