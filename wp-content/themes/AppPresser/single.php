<?php
/**
 * The Template for displaying all single posts.
 *
 * @package AppPresser Theme
 */

get_header(); ?>

<?php appp_title_header(); ?>

<div id="content" class="site-content" role="main">

<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part( 'content', 'single' ); ?>

	<?php appp_content_nav( 'nav-below' ); ?>

	<?php
		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() )
			comments_template();
	?>

<?php endwhile; // end of the loop. ?>

</div><!-- #content -->

<?php get_footer(); ?>