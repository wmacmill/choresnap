<?php
/**
 * The template for displaying the header search form. Searches listings.
 *
 * @package Listify
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( get_post_type_archive_link( 'job_listing' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php _e( 'Search for:', 'listify' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search', 'listify' ); ?>" value="" name="search_keywords" title="<?php echo esc_attr_e( 'Search for:', 'listify' ); ?>" />
	</label>
	<button type="submit" class="search-submit"></button>
</form>
