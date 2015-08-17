<?php if ( bp_has_notifications() ) : ?>

	<div id="pag-top" class="pagination no-ajax">
		<div class="pagination-links" id="notifications-pag-top">
			<?php bp_notifications_pagination_links(); ?>
		</div>
	</div>

	<?php bp_get_template_part( 'members/single/notifications/notifications-loop' ); ?>

	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pagination-links" id="notifications-pag-bottom">
			<?php bp_notifications_pagination_links(); ?>
		</div>
	</div>

<?php else : ?>

	<?php bp_get_template_part( 'members/single/notifications/feedback-no-notifications' ); ?>

<?php endif;