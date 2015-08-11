<?php
/**
 * The main template file.
 *
 * @package AppPresser Theme
 */

get_header();
appp_title_header(); ?>

<div id="content" class="site-content" role="main">

<?php if ( have_posts() ) : ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php
			/* Include the Post-Format-specific template for the content.
			 * If you want to overload this in a child theme then include a file
			 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
			 */
			get_template_part( 'content', get_post_format() );
		?>

	<?php endwhile; ?>

	<?php appp_content_nav( 'nav-below' ); ?>

<?php endif; ?>

</div><!-- #content -->

<?php get_footer(); ?>