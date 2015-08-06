<?php

class Listify_Customizer_Controls_Labels extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'labels';

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'label-singular' => array(
				'label' => __( 'Singular Label', 'listify' )
			),
			'label-plural' => array(
				'label' => __( 'Plural Label', 'listify' )
			),
			'region-bias' => array(
				'label' => __( 'Base Country', 'listify' ),
				'type' => 'select',
				'description' => __( 'This controls autocomplete priority, distance units, and more.', 'listify' ),
				'choices' => function_exists( 'wc' ) ? array_merge( array( '' => __( 'None', 'listify' ) ),
				wc()->countries->get_countries() ) : array()
			),
			'social-association' => array(
				'label' => __( 'Social Profiles', 'listify' ),
				'type' => 'select',
				'description' => __( 'If associated with a listing the fields will appear on the submission form', 'listify' ),
				'choices' => array(
					'listing' => __( 'Associate with listing', 'listify' ),
					'user' => __( 'Associate with user', 'listify' )
				)
			),
			'custom-submission' => array(
				'label' => __( 'Use "directory" submission fields', 'listify' ),
				'type' => 'checkbox',
			),
			'categories-only' => array(
				'label' => __( 'Use categories only', 'listify' ),
				'type' => 'checkbox',
				'description' => __( 'Categories will be used to create map markers, and types will
				be hidden from all other areas. Categories must be enabled in Listings > Settings.
				<br /><br />Refresh this page in your browser after saving.', 'listify' )
			)
		);

		return $wp_customize;
	}
	
}

new Listify_Customizer_Controls_Labels();
