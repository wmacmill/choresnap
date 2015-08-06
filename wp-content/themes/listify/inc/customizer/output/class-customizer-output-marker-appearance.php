<?php

class Listify_Customizer_Output_Marker_Appearance {
	
	public function __construct() {
		$this->css = new Listify_Customizer_CSS;

		add_action( 'listify_output_customizer_css', array( $this, 'markers' ) );
	}

	public function markers() {
		$terms = get_terms( listify_get_top_level_taxonomy(), array( 'hide_empty' => 0 ) );

		if ( is_wp_error( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			$color = listify_theme_mod( 'marker-color-' . $term->term_id );

			$this->css->add( array(
				'selectors' => array( '.map-marker.type-' . $term->term_id . ':after' ),
				'declarations' => array(
					'border-top-color' => $color
				)
			) );

			$this->css->add( array(
				'selectors' => array( '.map-marker.type-' . $term->term_id . ' i:after' ),
				'declarations' => array(
					'background-color' => $color
				)
			) );

			$this->css->add( array(
				'selectors' => array( '.map-marker.type-' . $term->term_id . ' i:before' ),
				'declarations' => array(
					'color' => $color
				)
			) );
		}
	}
}

new Listify_Customizer_Output_Marker_Appearance();
