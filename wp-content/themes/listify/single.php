<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Listify
 */

get_header(); ?>

	<?php if ( get_option( 'page_for_posts' ) ) : ?>
	<div <?php echo apply_filters( 'listify_cover', 'page-cover entry-cover', array( 'size' => 'full' ) ); ?>>
		<h1 class="page-title cover-wrapper"><?php echo get_the_title( get_option( 'page_for_posts', _x( 'Blog', 'blog page title', 'listify' ) ) ); ?></h1>
	</div>
	<?php endif; ?>

	<div id="primary" class="container">
		<div class="row content-area">

			<main id="main" class="site-main col-md-8 col-sm-7 col-xs-12" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content' ); ?>

					<?php comments_template(); ?>

				<?php endwhile; ?>

			</main>

			<?php get_sidebar(); ?>

		</div>
	</div>

<?php get_footer(); ?>
