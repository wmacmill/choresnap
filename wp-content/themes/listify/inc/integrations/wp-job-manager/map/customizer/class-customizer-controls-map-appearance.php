<?php

class Listify_Customizer_Controls_Map_Appearance extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'map-appearance';
		
		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		global $listify_job_manager;

		$this->controls = array(
			'map-appearance-scheme' => array(
				'label' => __( 'Color Scheme', 'listify' ),
				'type' => 'Listify_Customize_Color_Schemes_Control',
				'priority' => 0,
				'schemes' => $listify_job_manager->map->schemes->get_color_schemes(),
				'description' => sprintf( __( 'Some color schemes may show/hide extra information. Please <a href="#">read
				more</a> about creating a custom scheme.', 'listify' ), 'http://listify.astoundify.com/article/805-create-a-custom-map-color-scheme' )
			)
		);
		

		return $wp_customize;
	}

}

new Listify_Customizer_Controls_Map_Appearance();
