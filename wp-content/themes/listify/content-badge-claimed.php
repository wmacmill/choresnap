<?php
/**
 *
 */
?>
<a href="#what-is-claimed" class="claimed-ribbon popup-trigger">
	<span class="ion-checkmark-circled"></span>
	<span class="tooltip"><?php _e( 'Verified Listing', 'listify' ); ?></span>
</a>

<?php if ( is_singular( 'job_listing' ) ) : ?>
<div id="what-is-claimed" class="popup">
	<?php get_template_part( 'popup', 'content-verified-listing' ); ?>
</div>
<?php endif; ?>
