<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package AppPresser Theme
 */

get_header(); ?>

<?php if ( have_posts() ) : ?>

<?php appp_title_header(); ?>

<div id="content" class="site-content" role="main">

	<?php /* Start the Loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<?php
			/* Include the Post-Format-specific template for the content.
			 * If you want to overload this in a child theme then include a file
			 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
			 */
			get_template_part( 'content', 'archive' );
		?>

	<?php endwhile; ?>

	<?php appp_content_nav( 'nav-below' ); ?>

<?php else : ?>

	<?php get_template_part( 'no-results', 'archive' ); ?>

<?php endif; ?>

</div><!-- #content -->

<?php get_footer(); ?>