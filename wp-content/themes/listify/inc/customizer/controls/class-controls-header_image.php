<?php

class Listify_Customizer_Controls_Header_Image extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'header_image';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'fixed-header' => array(
				'label' => __( 'Fixed Header', 'listify' ),
				'type' => 'checkbox'
			)
		);

		return $wp_customize;
	}
	
}

new Listify_Customizer_Controls_Header_Image();
