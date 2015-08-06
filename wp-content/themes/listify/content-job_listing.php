<?php
/**
 * The template for displaying job listings (in a loop).
 *
 * @package Listify
 */
?>

<li id="job_listing-<?php the_ID(); ?>" <?php job_listing_class(); ?> <?php echo apply_filters(
'listify_job_listing_data', '', false ); ?>>

	<div class="content-box">

		<a href="<?php the_permalink(); ?>" class="job_listing-clickbox"></a>

		<header <?php echo apply_filters( 'listify_cover', 'job_listing-entry-header listing-cover' ); ?>>
            <?php do_action( 'listify_content_job_listing_header_before' ); ?>

			<div class="job_listing-entry-header-wrapper cover-wrapper">

				<div class="job_listing-entry-thumbnail">
					<div <?php echo apply_filters( 'listify_cover', 'list-cover' ); ?>></div>
				</div>
				<div class="job_listing-entry-meta">
					<?php do_action( 'listify_content_job_listing_meta' ); ?>
				</div>

			</div>

            <?php do_action( 'listify_content_job_listing_header_after' ); ?>
		</header><!-- .entry-header -->

		<footer class="job_listing-entry-footer">

			<?php do_action( 'listify_content_job_listing_footer' ); ?>

		</footer><!-- .entry-footer -->

	</div>
</li><!-- #post-## -->
