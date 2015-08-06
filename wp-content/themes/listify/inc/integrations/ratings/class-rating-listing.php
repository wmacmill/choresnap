<?php

class Listify_Rating_Listing extends Listify_Rating {

	public function __construct( $args = array() ) {
		parent::__construct( $args );

		add_filter( 'listify_listing_data', array( $this, 'listing_data' ) );
	}

	public function listing_data( $data ) {
		$data[ 'rating' ] = sprintf( _n( '%s Review', '%s Reviews', $this->count(), 'listify' ), $this->count() );

		return $data;
	}

	public function save() {
		global $wpdb;

		$query = $wpdb->prepare( "
			SELECT SUM(wpcm.meta_value)
			FROM $wpdb->comments AS wpc
			JOIN $wpdb->commentmeta AS wpcm
				ON wpc.comment_id  = wpcm.comment_id
			WHERE wpcm.meta_key = 'rating'
				AND wpc.comment_post_ID = %s
				AND wpc.comment_approved = '1'
		", $this->object_id );

		$total = $wpdb->get_var( $query );
		$votes = $this->count();

		if ( ! $total || $votes == 0 ) {
			update_post_meta( $this->object_id, 'rating', 0 );

			return;
		}

		$avg    = $total / $votes;
		$rating = round( round( $avg * 2 ) / 2, 1 );

		update_post_meta( $this->object_id, 'rating', $rating );

		return $rating;
	}

	public function get() {
		$this->rating = get_post_meta( $this->object_id, 'rating', true );

		if ( ! $this->rating ) {
			return 0;
		}

		return $this->rating;
	}

	public function count() {
		global $wpdb;
		$post_id = $this->object_id;

		$where = '';
		if ( $post_id > 0 ) {
			$where = $wpdb->prepare("WHERE comment_post_ID = %d AND comment_parent = 0 AND comment_approved = 1", $post_id);
		}

		$totals = (array) $wpdb->get_var("
			SELECT COUNT( * ) AS total
			FROM {$wpdb->comments}
			{$where}
		");

		if ( null == $totals ) {
			return 0;
		} else {
			return $totals[0];
		}
	}

}
