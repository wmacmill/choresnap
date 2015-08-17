<?php do_action( 'bp_before_member_messages_loop' ); ?>

<?php if ( bp_has_message_threads( bp_ajax_querystring( 'messages' ) ) ) : ?>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_pagination' ); ?>

	<?php do_action( 'bp_before_member_messages_threads'   ); ?>

	<ul id="message-threads" class="messages-notices">
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>

			<li id="m-<?php bp_message_thread_id(); ?>" class="<?php bp_message_css_class(); ?><?php if ( bp_message_thread_has_unread() ) : ?> unread<?php else: ?> read<?php endif; ?>">

				<div class="thread-avatar">
					<a href="<?php bp_message_thread_view_link(); ?>" title="<?php esc_attr_e( "View Message", "appbuddy" ); ?>"><?php bp_message_thread_avatar(); ?></a>
					<div class="thread-from"<?php bp_message_thread_from(); ?></div>
				</div>

				<div class="thread-info">
					<p class="message-title"><a href="<?php bp_message_thread_view_link(); ?>" title="<?php esc_attr_e( "View Message", "appbuddy" ); ?>"><?php bp_message_thread_subject(); ?></a></p>
				</div>

				<?php do_action( 'bp_messages_inbox_list_item' ); ?>

				<div class="thread-options">
					<a class="button confirm" href="<?php bp_message_thread_delete_link(); ?>" title="<?php esc_attr_e( "Delete Message", "appbuddy" ); ?>"><?php _e( 'Delete', 'appbuddy' ); ?></a>
				</div>

			</li>

		<?php endwhile; ?>
	</ul><!-- #message-threads -->


	<?php do_action( 'bp_after_member_messages_threads' ); ?>

	<?php do_action( 'bp_after_member_messages_options' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'appbuddy' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ); ?>
