<?php
/**
 * Template Name: Layout: Full Width
 *
 * @package Listify
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div <?php echo apply_filters( 'listify_cover', 'page-cover entry-cover', array( 'size' => 'full' ) ); ?>>
			<h1 class="page-title cover-wrapper"><?php the_title(); ?></h1>
		</div>

		<?php do_action( 'listify_page_before' ); ?>

		<div id="primary" class="container">
			<div class="content-area">

				<main id="main" class="site-main" role="main">

					<?php if ( listify_has_integration( 'woocommerce' ) ) : ?>
						<?php wc_print_notices(); ?>
					<?php endif; ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template(); ?>

				</main>

			</div>
		</div>

	<?php endwhile; ?>

<?php get_footer(); ?>
