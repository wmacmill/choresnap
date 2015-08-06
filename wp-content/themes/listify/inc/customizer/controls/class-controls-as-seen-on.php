<?php

class Listify_Customizer_Controls_As_Seen_On extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'as-seen-on';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'as-seen-on-title' => array(
				'label' => __( 'Title', 'listify' )
			),
			'as-seen-on-logos' => array(
				'label' => __( 'Logos', 'listify' ),
				'type' => 'Listify_Customize_Textarea_Control'
			)
		);

		return $wp_customize;
	}

}

new Listify_Customizer_Controls_As_Seen_On();
