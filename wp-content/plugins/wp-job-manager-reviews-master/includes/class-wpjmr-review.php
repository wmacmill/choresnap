<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMR_Review_Handler
 *
 *	Handle all reviews.
 *
 *	@class       WPJMR_Review_Handler
 *	@version     1.0.0
 *	@author      Jeroen Sormani
 */
class WPJMR_Review {

	/**
	 * Construct.
	 *
	 * Initialize this class including hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Save comment meta
		add_action( 'comment_post', array( $this, 'save_comment_review' ) );

		// Replace themes 'comments.php' with
		add_filter( 'get_comment_text', array( $this, 'review_comment_text' ), 10, 3 );

		// Add stars to form
		add_action( 'comment_form_top', array( $this, 'comment_form_stars' ) );

	}


	/**
	 * Add stars to comment.
	 *
	 * Add the stars based on categories to default comment text.
	 *
	 * @since 1.0.0
	 *
	 * @param string $comment_content Text of the comment.
	 * @param object $comment         The comment object.
	 * @param array  $args            An array of arguments.
	 */
	public function review_comment_text( $content, $comment, $args ) {
		if ( 0 != $comment->comment_parent || ! is_singular( 'job_listing' ) ) {
			return $content;
		}

		ob_start();

			?><div id='wpjmr-list-reviews'><?php

				$ratings 	= WPJMR()->review->get_ratings( get_comment_ID() );
				$categories = WPJMR()->wpjmr_get_review_categories();
				foreach ( $ratings as $category => $rating ) : ?>
					<div class='star-rating'>
						<div class='star-rating-title'><?php echo isset( $categories[ $category ] ) ? $categories[ $category ] : $category; ?></div>
						<?php for ( $i = 0; $i < WPJMR()->wpjmr_get_count_stars(); $i++ ) : ?>
							<?php if ( $i < $rating ) : ?>
								<span class="dashicons dashicons-star-filled"></span><?php else : ?><span class="dashicons dashicons-star-empty"></span><?php endif; ?>
						<?php endfor; ?>
					</div>
				<?php endforeach; ?>
			</div><?php

			$stars = ob_get_contents();
		ob_end_clean();

		$content = $stars . $content;
		return $content;

	}


	/**
	 * Comment form stars.
	 *
	 * Add stars to the comment form based on review categories. Done via action hook.
	 *
	 * @since 1.0.0
	 */
	public function comment_form_stars() {
		if ( ! is_singular( 'job_listing' ) ) {
			return;
		}

		?><div id='wpjmr-submit-ratings' class='review-form-stars'>

			<div class='star-ratings'>

				<?php foreach ( WPJMR()->wpjmr_get_review_categories() as $category_slug => $category ) : ?>

					<div class='rating-row'>

						<label for='<?php echo $category_slug; ?>'><?php echo $category; ?></label>

							<div class='choose-rating' data-rating-category='<?php echo $category_slug; ?>'>
								<?php for ( $i = WPJMR()->wpjmr_get_count_stars(); $i > 0 ; $i-- ) : ?>
										<span data-star-rating='<?php echo $i; ?>' class="star dashicons dashicons-star-empty"></span>
								<?php endfor; ?>
								<input type='hidden' class='required' name='star-rating-<?php echo $category_slug; ?>' value=''>

							</div>

					</div>

				<?php endforeach; ?>

			</div>

		</div><?php

	}


	/**
	 * Save comment meta.
	 *
	 * Save the ratings as comment meta in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param int @comment_id ID of the current comment.
	 */
	public function save_comment_review( $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( 0 != $comment->comment_parent ) {
			return;
		}

		$review_categories = WPJMR()->wpjmr_get_review_categories();
		$review_average    = 0;

		// Save review categories in database for this review.
		update_comment_meta( $comment_id, 'review_categories', $review_categories );

		foreach ( $review_categories as $category_slug => $review_category ) :

			if ( isset ( $_POST['star-rating-' . $category_slug ] ) ) :
				$value = $_POST['star-rating-' . $category_slug ];
				$review_average += $value;

				update_comment_meta( $comment_id, 'star-rating-' . $category_slug, $value );
			endif;

		endforeach;

		if ( $review_average > 0 ) {
			$review_average = floor( $review_average / count( $review_categories ) );
		}

		update_comment_meta( $comment_id, 'review_average', $review_average );

	}


	/**
	 * Get reviews.
	 *
	 * Get all the reviews based on post_id.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int $post_id ID of the current listing.
	 * @return 	array List of ratings with slug and rating.
	 */
	public function get_reviews_by_id( $post_id = '' ) {

		if ( ! is_integer( $post_id ) || ! $post_id ) :
			$post_id = get_the_ID();
		endif;

		// return if its not an job listing.
		if ( 'job_listing' != get_post_type( $post_id ) ) :
			return;
		endif;

		$args = array(
			'post_id' => $post_id,
			'parent' => 0
		);
		$reviews = get_comments( $args );

		return $reviews;

	}

	/**
	 * Ratings.
	 *
	 * Get review categories saved in database; these are saved for
	 * future compatibility since categories might change in the future.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int @comment_id ID of the current comment.
	 * @return 	array List of ratings with slug and rating.
	 */
	public function get_ratings( $comment_id ) {

		$review_categories = get_comment_meta( $comment_id, 'review_categories', true );

		if ( ! $review_categories ) :
			return array();
		endif;

		$ratings = array();
		foreach ( $review_categories as $category_slug => $review_category ) :

			$ratings[ $category_slug ] = get_comment_meta( $comment_id, 'star-rating-' . $category_slug, true );

		endforeach;

		return $ratings;

	}


	/**
	 * Average rating review.
	 *
	 * Get the average rating of a review.
	 * NOTE: this is the average of a single review (all categories), not the average of the post.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int $comment_id ID of the current comment.
	 * @return 	int Average of the review.
	 */
	public function average_rating_review( $comment_id ) {
		$average = get_comment_meta( $comment_id, 'review_average', true );

		if ( ! $average ) {
			$average = 0;
		}

		return number_format( $average, 1, '.', ',' );
	}


	/**
	 * Average rating listing.
	 *
	 * Get the average rating of a liting.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int $post_id 	ID of the current listing.
	 * @return 	int Average 	of the review.
	 */
	public function average_rating_listing( $post_id ) {

		$reviews = $this->get_reviews_by_id( $post_id );

		$reviews_added = 0;
		foreach ( $reviews as $review ) :

			$reviews_added += $this->average_rating_review( $review->comment_ID );

		endforeach;

		// Check if $reviews exists and is not 0
		if ( $reviews ) :
			$review_average = $reviews_added / count( $reviews );
		else :
			return 0;
		endif;

		return round( $review_average, apply_filters( 'wpjmr_review_average_round', 1 ) );

	}


	/**
	 * Review count.
	 *
	 * Return the number of reviews.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int $post_id ID of the current listing.
	 * @return 	int Review count.
	 */
	public function review_count( $post_id = '' ) {

		if ( ! is_integer( $post_id ) || ! $post_id ) :
			$post_id = get_the_ID();
		endif;

		$review_count = count( $this->get_reviews_by_id( $post_id ) );

		return $review_count;

	}


	/**
	 * Get stars.
	 *
	 * Get the stars according to the review average.
	 *
	 * @since 1.0.0
	 *
	 * @see wp_list_comments()
	 *
	 * @param 	int 	$post_id 	ID to get the stars for.
	 * @param 	int 	$count		Custom count of stars.
	 * @return 	string 				HTML containing stars.
	 */
	public function get_stars( $post_id = '', $count = '' ) {

		if ( ! is_integer( $post_id ) || ! $post_id ) :
			$post_id = get_the_ID();
		endif;

		$stars = $this->average_rating_listing( $post_id );

		ob_start(); ?>

		<span class='stars-rating'>
			<?php for ( $i = 0; $i < WPJMR()->wpjmr_get_count_stars(); $i++ ) : ?>

				<?php if ( $i < $stars ) : ?>
					<span class="dashicons dashicons-star-filled"></span>
				<?php else : ?>
					<span class="dashicons dashicons-star-empty"></span>
				<?php endif; ?>

			<?php endfor; ?>
		</span>

			<?php
			$return = ob_get_contents();
		ob_end_clean();

		return $return;

	}


}