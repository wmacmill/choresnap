<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Listify
 */

function listify_wp_video_shortcode( $html ) {
	$html = str_replace( 'controls="controls"', '', $html );

	return $html;
}

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 */
function listify_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'listify' ), max( $paged, $page ) );

	return $title;
}
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	add_filter( 'wp_title', 'listify_wp_title', 10, 2 );
}

/**
 * Return an ID of an attachment by searching the database with the file URL.
 *
 * First checks to see if the $url is pointing to a file that exists in
 * the wp-content directory. If so, then we search the database for a
 * partial match consisting of the remaining path AFTER the wp-content
 * directory. Finally, if a match is found the attachment ID will be
 * returned.
 *
 * @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg)
 *
 * @return int|null $attachment Returns an attachment ID, or null if no attachment is found
 */
function listify_get_attachment_id_by_url( $url ) {
	// Split the $url into two parts with the wp-content directory as the separator
	$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	// Get the host of the current site and the host of the $url, ignoring www
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	// Return nothing if there aren't any $url parts or if the current host and $url host do not match
	if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
	}

	// Now we're going to quickly search the DB for any attachment GUID with a partial path match
	// Example: /uploads/2013/05/test-image.jpg
	global $wpdb;

	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

	// Returns null if no attachment is found
	return $attachment[0];
}

/**
 * Remove ellipsis from the excerpt
 */
function listify_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'listify_excerpt_more' );

function listify_widget_posts_args( $args ) {
	if ( ! is_author() ) {
		return $args;
	}

	$args[ 'author' ] = get_the_author_meta( 'ID' );

	return $args;
}
add_filter( 'widget_posts_args', 'listify_widget_posts_args' );

// Shortcodes in widgets
add_filter( 'widget_text', 'do_shortcode' );
