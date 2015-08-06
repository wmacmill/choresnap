<?php

class Listify_WP_Job_Manager_Map_Schemes {

	public function __construct() {
		add_filter( 'listify_map_settings', array( $this, 'apply_color_scheme' ) );
		add_filter( 'listify_single_map_settings', array( $this, 'apply_color_scheme' ) );
	}

	public function get_color_scheme() {
		return listify_theme_mod( 'map-appearance-scheme' );
	}

	public function default_styles() {
		$default = apply_filters( 'listify_map_default_styles', array(
			array(
				'featureType' => "poi",
				'stylers' => array(
					array(
						'visibility' => "off"
					)
				)
			)
        ) );

        return $default;
	}

	public function apply_color_scheme( $settings ) {
		$scheme = $this->get_color_scheme();
		$scheme = $scheme . '.json';

		$styles = array();
		$file   = false;

		$custom = trailingslashit( get_stylesheet_directory() ) . $scheme;
		$included = trailingslashit( dirname( __FILE__ ) ) . trailingslashit( 'schemes' ) . $scheme;

		if ( file_exists( $custom ) ) {
			$file = file_get_contents( $custom );
		} elseif ( file_exists( $included ) ) {
			$file = file_get_contents( $included );
		}

		if ( $file ) {
			$styles = json_decode( $file, true );
		}

		$settings[ 'mapOptions' ][ 'styles' ] = array_merge( $this->default_styles(), $styles );

		return $settings;
	}

	public function get_color_schemes() {
		$schemes = apply_filters( 'listify_map_color_schemes', array(
			'Default' => array(
				'color-1' => '#e9e5dc',
				'color-2' => '#fa9e25',
				'color-3' => '#a9cafe',
				'color-4' => '#ffffff',
				'color-5' => '#ebd2cf'
			),
			'Dark' => array(
				'color-1' => '#2a2a2a',
				'color-2' => '#333333',
				'color-3' => '#272727',
				'color-4' => '#666666',
				'color-5' => '#333333'
			),
			'Apple' => array(
				'color-1' => '#f7f1df',
				'color-2' => '#ffe15f',
				'color-3' => '#a2daf2',
				'color-4' => '#ffffff',
				'color-5' => '#ede3d0'
			),
			'Gowalla' => array(
				'color-1' => '#f0ede5',
				'color-2' => '#fa9525',
				'color-3' => '#d9ebff',
				'color-4' => '#ffffff',
				'color-5' => '#ede3d0'
			)
		) );

		return $schemes;
	}

}
