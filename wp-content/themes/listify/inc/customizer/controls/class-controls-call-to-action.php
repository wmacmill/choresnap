<?php

class Listify_Customizer_Controls_Call_To_Action extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'call-to-action';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'call-to-action-display' => array(
				'label' => __( 'Display this section', 'listify' ),
				'type' => 'checkbox'
			),
			'call-to-action-title' => array(
				'label' => __( 'Title', 'listify' )
			),
			'call-to-action-description' => array(
				'label' => __( 'Description', 'listify' ),
				'type' => 'Listify_Customize_Textarea_Control'
			),
			'call-to-action-button-text' => array(
				'label' => __( 'Button Label', 'listify' )
			),
			'call-to-action-button-href' => array(
				'label' => __( 'Button Link', 'listify' )
			),
			'call-to-action-button-subtext' => array(
				'label' => __( 'Button Subtext', 'listify' )
			)
		);

		return $wp_customize;
	}

}

new Listify_Customizer_Controls_Call_To_Action();
