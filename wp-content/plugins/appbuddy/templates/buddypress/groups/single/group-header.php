<div id="item-header-avatar">
		<?php bp_group_avatar(); ?>
</div><!-- #item-header-avatar -->

<div id="item-header-content">
	<div class="user-nicename"><?php bp_group_name(); ?></div>

	<?php do_action( 'bp_before_group_header_meta' ); ?>

	<div id="item-meta">

		<?php bp_group_description(); ?>

		<div id="item-buttons">

			<?php do_action( 'bp_group_header_actions' ); ?>

		</div><!-- #item-buttons -->
		
		<span class="group-type"><?php bp_group_type(); ?></span>

		<?php do_action( 'bp_group_header_meta' ); ?>

	</div>
</div><!-- #item-header-content -->

<?php
do_action( 'template_notices' );
?>