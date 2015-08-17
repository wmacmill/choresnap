<?php do_action( 'bp_before_notices_loop' ); ?>

<?php if ( bp_has_message_threads() ) : ?>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_notices_pagination' ); ?>
	<?php do_action( 'bp_before_notices' ); ?>

	<ul id="message-threads" class="messages-notices">

		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
			<li id="notice-<?php bp_message_notice_id(); ?>" class="<?php bp_message_css_class(); ?>">
				<div class="notice-title">
					<strong><?php bp_message_notice_subject(); ?></strong>

				</div>
				<p><?php bp_message_notice_text(); ?></p>
				<div>

					<?php if ( bp_messages_is_active_notice() ) : ?>

						<strong><?php bp_messages_is_active_notice(); ?></strong>

					<?php endif; ?>

					<span class=""><?php _e( 'Sent:', 'appbuddy' ); ?> <?php bp_message_notice_post_date(); ?></span>
				</div>

				<?php do_action( 'bp_notices_list_item' ); ?>

				<div class="action">
					<a class="button" href="<?php bp_message_activate_deactivate_link(); ?>" class="confirm"><?php bp_message_activate_deactivate_text(); ?></a>
					<a class="button" href="<?php bp_message_notice_delete_link(); ?>" class="confirm" title="<?php esc_attr_e( "Delete Message", "appbuddy" ); ?>">x</a>
				</div>
				<div class="clear"></div>
			</li>


		<?php endwhile; ?>


	</ul><!-- #message-threads -->

	<?php do_action( 'bp_after_notices' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no notices were found.', 'appbuddy' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_notices_loop' ); ?>



