<div id="item-header-avatar">
	<?php bp_displayed_user_avatar( 'type=full' ); ?>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		<h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
	<?php endif; ?>

</div><!-- #item-header-content -->

<div id="item-buttons" class="button-bar">

	<?php do_action( 'bp_member_header_actions' ); ?>

</div><!-- #item-buttons -->