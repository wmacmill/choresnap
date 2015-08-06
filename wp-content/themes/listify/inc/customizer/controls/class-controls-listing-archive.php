<?php

class Listify_Customizer_Controls_Listing_Archive extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'listing-archive';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'listing-archive-output' => array(
				'label' => __( 'Display', 'listify' ),
				'type' => 'select',
				'choices' => array(
					'results' => __( 'Results Only', 'listify' ),
					'map-results' => __( 'Map + Results', 'listify' )
				)
			),
			'listing-archive-map-position' => array(
				'label' => __( 'Map Position', 'listify' ),
				'type'  => 'select',
				'choices' => array(
					'side' => __( 'Side (fixed)', 'listify' ),
					'top'  => __( 'Top', 'listify' )
				)
			),
			'listing-archive-display-style' => array(
				'label' => __( 'Display Style', 'listify' ),
				'type'  => 'select',
				'choices' => array(
					'grid' => __( 'Grid', 'listify' ),
					'list' => __( 'List', 'listify' )
				)
			)
		);

		return $wp_customize;
	}

	public function get_icon_list() {
		global $iconlist;

		include_once( get_template_directory() . '/inc/icon-list.php' );

		$list = array();

		$list[ '' ] = __( 'Default', 'listify' );

		foreach ( $iconlist as $meh => $icon ) {
			$list[$icon->name] = $icon->name;
		}

		return $list;
	}
}

new Listify_Customizer_Controls_Listing_Archive();
