<?php
/**
 * The template for displaying single image attachments.
 *
 * While this is for a single content item the header and footer
 * are not loaded since this is loaded via a modal overlay.
 *
 * @package Listify
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div <?php echo apply_filters( 'listify_cover', 'page-cover entry-cover' ); ?>>
			<h1 class="page-title cover-wrapper"><?php the_title(); ?></h1>
		</div>

		<?php do_action( 'listify_page_before' ); ?>

		<div id="primary" class="container">
			<div class="row content-area">

				<main id="main" class="site-main col-md-8 col-md-offset-2" role="main">

					<?php if ( get_post()->post_parent != 0 ) : ?>

					<p class="back-to-listing">
						<a href="<?php echo get_permalink( get_post()->post_parent ); ?>" class="ion-chevron-left"><?php printf( __( 'Back to %s', 'listify' ), get_the_title( get_post()->post_parent ) ); ?></a>
					</p>

					<?php endif; ?>

					<div class="single-job_listing-attachment">
						<?php echo wp_get_attachment_image( get_the_ID(), 'fullsize' ); ?>
					</div>

					<?php comments_template(); ?>

				</main>

			</div>
		</div>

	<?php endwhile; ?>

<?php get_footer(); ?>