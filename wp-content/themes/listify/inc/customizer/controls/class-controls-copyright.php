<?php

class Listify_Customizer_Controls_Copyright extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'copyright';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'footer-style' => array(
				'label' => __( 'Display Style', 'listify' ),
				'type' => 'select',
				'choices' => array(
					'dark' => __( 'Dark', 'listify' ),
					'light' => __( 'Transparent', 'listify' )
				)
			),
			'copyright-text' => array(
				'label' => __( 'Copyright Text', 'listify' )
			)
		);

		return $wp_customize;
	}

}

new Listify_Customizer_Controls_Copyright();
