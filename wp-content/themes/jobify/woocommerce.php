<?php
/**
 * Single Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jobify
 * @since Jobify 1.0
 */

get_header(); ?>

	<?php woocommerce_content(); ?>
	<?php do_action( 'jobify_loop_after' ); ?>
	

<?php get_footer(); ?>