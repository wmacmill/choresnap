<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Listify
 */

get_header(); ?>

	<?php if ( is_home() ) : ?>
	<div <?php echo apply_filters( 'listify_cover', 'page-cover entry-cover', array( 'size' =>
	'full' ) ); ?>>
		<h1 class="page-title cover-wrapper"><?php echo get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) :  _x( 'Blog', 'blog page title', 'listify' ); ?></h1>
	</div>
	<?php endif; ?>

	<div id="primary" class="container">
	    <div class="row content-area">

			<main id="main" class="site-main col-md-8 col-sm-7 col-xs-12" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content' ); ?>

				<?php endwhile; ?>

				<?php get_template_part( 'content', 'pagination' ); ?>

			</main>

			<?php get_sidebar(); ?>

		</div>
	</div>

<?php get_footer(); ?>
