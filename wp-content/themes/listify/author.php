<?php
/**
 * The template for displaying Author pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Listify
 */

// Only use this template if we have custom data to load.
if ( ! listify_has_integration( 'wp-job-manager' ) ) {
	return locate_template( array( 'archive.php' ), true );
}

global $listify_strings;

$author = get_query_var( 'author' );
$author = get_user_by( 'id', $author );

$first = get_the_author_meta( 'first_name', $author->ID );
$display = $first ? $first : get_the_author_meta( 'display_name', $author->ID );

get_header(); ?>

	<div <?php echo apply_filters( 'listify_cover', 'page-cover entry-cover' ); ?>>
		<div class="page-title cover-wrapper">
			<div class="author-title">
				<div class="author-name">
					<?php echo get_avatar($author->ID, 150); ?>

					<h1><?php echo esc_attr( $display ); ?></h1>
				</div>
				<p class="author-meta">
					<span class="listing-count"><?php printf( __( '%d Listed', 'listify' ), $wp_query->found_posts ); ?></span>

					<?php if ( listify_has_integration( 'wp-job-manager-bookmarks' ) ) : ?>
						<?php global $job_manager_bookmarks; ?>

						<span class="favorite-count"><?php printf( 
							_n( '%d Favorite', '%d Favorites', count( $job_manager_bookmarks->get_user_bookmarks( $author->ID ) ), 'listify' ), 
							count( $job_manager_bookmarks->get_user_bookmarks( $author->ID ) ) ); 
						?></span>
					<?php endif; ?>
				</p>
			</div>
		</div>
	</div>

	<div id="primary" class="container">
		<div class="row content-area">

			<main id="main" class="site-main col-md-8 col-sm-7 col-xs-12" role="main">

				<?php if ( '' != get_the_author_meta( 'description', $author->ID ) ) : ?>
				<section id="about">
					<div class="content-box">

						<h3 class="widget-title ion-person"><?php _e( 'About Me', 'listify' ); ?></h3>

						<?php the_author_meta( 'description', $author->ID ); ?>

					</div>
				</section>
				<?php endif; ?>

				<?php do_action( 'listify_author_profile_after_about' ); ?>

				<section id="listings">

					<h3 class="section-title"><?php printf( __( '%s\'s Listings (%d)', 'listify' ), $display, $wp_query->found_posts ); ?></h3>

					<?php if ( have_posts() ) : ?>

						<ul class="job_listings">
							<?php while ( have_posts() ) : the_post(); ?>

								<?php get_template_part( 'content', 'job_listing' ); ?>

							<?php endwhile; ?>
						</ul>

						<?php get_template_part( 'content', 'pagination' ); ?>

					<?php else : ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; wp_reset_query(); ?>

				</section>

				<?php do_action( 'listify_author_profile_after_listings' ); ?>

				<?php if ( listify_has_integration( 'wp-job-manager-bookmarks' ) ) : ?>

					<?php
						global $job_manager_bookmarks;

						$bookmarks = $job_manager_bookmarks->get_user_bookmarks( $author->ID );

						if ( ! empty( $bookmarks ) ) :
							$bookmarks = wp_list_pluck( $bookmarks, 'post_id' );

							$wp_query = new WP_Query( array(
								'post_type' => 'job_listing',
								'post__in' => $bookmarks,
								'post_status' => 'publish',
								'posts_per_page' => 6,
								'is_author' => true
							) );
					?>

					<section id="bookmarks">

						<h3 class="section-title"><?php printf( __( '%s\'s
						Favorites (%d)', 'listify' ), $display, $wp_query->found_posts ); ?></h3>

						<?php if ( $wp_query->have_posts() ) : ?>

							<ul class="job_listings">
								<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

									<?php get_template_part( 'content', 'job_listing' ); ?>

								<?php endwhile; ?>
							</ul>

							<?php get_template_part( 'content', 'pagination' ); ?>

						<?php else : ?>

							<?php get_template_part( 'content', 'none' ); ?>

						<?php endif; wp_reset_query(); ?>

					</section>

					<?php endif; ?>

					<?php do_action( 'listify_author_profile_after_bookmarks' ); ?>

				<?php endif; ?>

			</main>

			<div id="secondary" class="widget-area col-md-4 col-sm-5 col-xs-12" role="complementary">

				<?php
					the_widget(
						'WP_Widget_Recent_Posts',
						array(
							'title' => __( 'Recent Blog Posts', 'listify' ),
							'icon'  => 'clipboard'
						),
						array(
							'id'            => 'widget-area-sidebar-1',
							'before_widget' => '<aside class="widget widget_recent_posts">',
							'after_widget'  => '</aside>',
							'before_title'  => '<h3 class="widget-title ion-clipboard">',
							'after_title'   => '</h3>',
						)
					);
				?>

				<?php if ( listify_has_integration( 'woocommerce' ) ) : ?>

					<?php
						the_widget(
							'Listify_Widget_Listing_Social_Profiles',
							array(
								'title' => __( 'Social Profiles', 'listify' ),
								'icon' => 'share'
							),
							array(
								'before_widget' => '<aside id="widget-social-profiles" class="widget">',
								'after_widget'  => '</aside>',
								'before_title'  => '<h1 class="widget-title %s">',
								'after_title'   => '</h1>',
								'widget_id'     => ''
							)
						);
					?>

				<?php endif; ?>

				<?php do_action( 'listify_author_profile_after_sidebar' ); ?>

			</div><!-- #secondary -->

		</div>
	</div>

<?php get_footer(); ?>
