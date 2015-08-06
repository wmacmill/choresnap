<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMR_Shortcodes
 *
 *	Handle all reviews.
 *
 *	@class       WPJMR_Shortcodes
 *	@version     1.0.0
 *	@author      Jeroen Sormani
 */
class WPJMR_Shortcodes {

	/**
	 * Construct.
	 *
	 * Initialize this class including hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		
		// [review_stars]
		add_shortcode( 'review_stars', array( $this, 'shortcode_review_stars' ) );
		
		// [review_average]
		add_shortcode( 'review_average', array( $this, 'shortcode_review_average' ) );
		
		// [review_count]
		add_shortcode( 'review_count', array( $this, 'shortcode_review_count' ) );
		
		// [reviews]
		add_shortcode( 'reviews', array( $this, 'shortcode_reviews' ) );
		
		//[review_form]
		add_shortcode( 'review_form', array( $this, 'shortcode_review_form' ) );
		
		
	}
	

	/**
	 * [review_stars].
	 *
	 * A shortcode for the review stars..
	 *
	 * @since 1.0.0
	 * 
	 * @param array		@atts 		Attributes given in the shortcode.
	 * @param string 	@content 	Content of the shortcode.
	 */
	public function shortcode_review_stars( $atts = array(), $content = '' ) {
		
		extract( shortcode_atts( array(
			'post_id' => get_the_ID(),
		), $atts ) );
		
		$post_id = (int) $post_id;
		
		if ( ! $post_id || ! is_integer( $post_id ) ) :
			return;
		endif;
		
		$return = WPJMR()->review->get_stars( $post_id );
		
		return '<span class="review-stars">' . $return . '</span>';
		
	}
	
	
	/**
	 * [review_average].
	 *
	 * A shortcode for the review average.
	 *
	 * @since 1.0.0
	 * 
	 * @param array		@atts 		Attributes given in the shortcode.
	 * @param string 	@content 	Content of the shortcode.
	 */
	public function shortcode_review_average( $atts = array(), $content = '' ) {
		
		extract( shortcode_atts( array(
			'post_id' => get_the_ID(),
		), $atts ) );
		
		$post_id = (int) $post_id;
		
		if ( ! $post_id || ! is_integer( $post_id ) ) :
			return;
		endif;
		
		$return = WPJMR()->review->average_rating_listing( $post_id );
		
		return '<span class="review-average">' . $return . '</span>';
		
	}


	/**
	 * [review_count].
	 *
	 * A shortcode for the review count.
	 *
	 * @since 1.0.0
	 * 
	 * @param array		@atts 		Attributes given in the shortcode.
	 * @param string 	@content 	Content of the shortcode.
	 */
	public function shortcode_review_count( $atts = array(), $content = '' ) {
		
		extract( shortcode_atts( array(
			'post_id' => get_the_ID(),
		), $atts ) );
		
		$post_id = (int) $post_id;
		
		if ( ! $post_id || ! is_integer( $post_id ) ) :
			return;
		endif;
		
		$return = WPJMR()->review->review_count( $post_id );
		
		return '<span class="review-count">' . $return . '</span>';
		
	}
		

	/**
	 * [reviews].
	 *
	 * A shortcode to get the reviews.
	 *
	 * @since 1.0.0
	 * 
	 * @param array		@atts 		Attributes given in the shortcode.
	 * @param string 	@content 	Content of the shortcode.
	 */
	public function shortcode_reviews( $atts = array(), $content = '' ) {
		
		extract( shortcode_atts( array(
			'post_id' => get_the_ID(),
		), $atts ) );
		
		$post_id = (int) $post_id;
		
		if ( ! $post_id || ! is_integer( $post_id ) ) :
			return;
		endif;
		
		ob_start();
			wp_list_comments( array( 'callback' => array( WPJMR()->review, 'wpjmr_comments' ) ), WPJMR()->review->get_reviews_by_id( $post_id ) );
			$return = ob_get_contents();
		ob_end_clean();
		
		return $return;
		
	}
	
	
	/**
	 * [review_form].
	 *
	 * A shortcode to get the review form.
	 *
	 * @since 1.0.0
	 * 
	 * @param array		@atts 		Attributes given in the shortcode.
	 * @param string 	@content 	Content of the shortcode.
	 */
	public function shortcode_review_form( $atts = array(), $content = '' ) {
		
		extract( shortcode_atts( array(
			'' => '',
		), $atts ) );
		
		ob_start();
			WPJMR()->wpjmr_get_template( 'review-form.php' );
		$return = ob_get_contents();
		ob_end_clean();
		
		return $return;
		
	}

}
new WPJMR_Shortcodes();