<?php

class Listify_Customizer_Controls_Map_Behavior extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'map-behavior';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'map-behavior-api-key' => array(
				'label' => __( 'Google Maps API Key (optional)', 'listify' )
			),
			'map-behavior-trigger' => array(
				'label' => __( 'Info Bubble Trigger', 'listify' ),
				'type' => 'select',
				'choices' => array(
					'mouseover' => __( 'Hover', 'listify' ),
					'click' => __( 'Click', 'listify' )
				)
			),
			'map-behavior-clusters' => array(
				'label' => __( 'Use Clusters', 'listify' ),
				'type' => 'checkbox',
			),
			'map-behavior-grid-size' => array(
				'label' => __( 'Cluster Grid Size (px)', 'listify' )
			),
			'map-behavior-autofit' => array(
				'label' => __( 'Autofit on load', 'listify' ),
				'type' => 'checkbox'
			),
			'map-behavior-center' => array(
				'label' => __( 'Default Center Coordinate', 'listify' )
			),
			'map-behavior-zoom' => array(
				'label' => __( 'Default Zoom Level', 'listify' ),
				'type' => 'select',
				'choices' => array( '1' => '1', '2' => '2', '3' => '3', '4' =>
				'4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9',
				'10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' =>
				'14', '15' => '15', '16' => '16', '17' => '17', '18' => '18' ),
			),
			'map-behavior-max-zoom' => array(
				'label' => __( 'Max Zoom Level', 'listify' ),
				'type' => 'select',
				'choices' => array( '1' => '1', '2' => '2', '3' => '3', '4' =>
				'4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9',
				'10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' =>
				'14', '15' => '15', '16' => '16', '17' => '17', '18' => '18' ),
			),
		);
		
		if ( ! listify_has_integration( 'facetwp' ) ) {
			global $listify_job_manager;

			$this->controls[ 'map-behavior-search-min' ] = array(
				'label' => sprintf( __( 'Search Radius Min (%s)', 'listify' ), $listify_job_manager->map->template->unit() )
			);

			$this->controls[ 'map-behavior-search-max' ] = array(
				'label' => sprintf( __( 'Search Radius Max (%s)', 'listify' ), $listify_job_manager->map->template->unit() )
			);

			$this->controls[ 'map-behavior-search-default' ] = array(
				'label' => sprintf( __( 'Search Radius Default (%s)', 'listify' ), $listify_job_manager->map->template->unit() )
			);
		}

		return $wp_customize;
	}

	public function get_icon_list() {
		global $iconlist;

		nclude_once( get_template_directory() . '/inc/icon-list.php' );

		$list = array();

		$list[ '' ] = __( 'Default', 'listify' );

		foreach ( $iconlist as $meh => $icon ) {
			$list[$icon->name] = $icon->name;
		}

		return $list;
	}
}

new Listify_Customizer_Controls_Map_Behavior();
