<?php

class Listify_Customizer_Controls_Colors extends Listify_Customizer_Controls {

	public $controls = array();

	public function __construct() {
		$this->section = 'colors';
		$this->priority = new Listify_Customizer_Priority(49, 1);

		parent::__construct();

		$this->scheme = listify_get_color_scheme();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_scripts' ) );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'color-scheme' => array(
				'label' => __( 'Color Scheme', 'listify' ),
				'type' => 'Listify_Customize_Color_Schemes_Control',
				'schemes' => listify_get_color_schemes(),
				'priority' => 0
			),
			'color-header-background' => array(
				'label' => __( 'Header Background Color', 'listify' ),
				'type'    => 'WP_Customize_Color_Control'
			),
			'color-navigation-text' => array(
				'label' => __( 'Navigation Text Color', 'listify' ),
				'type'    => 'WP_Customize_Color_Control'
			),
			'color-link' => array(
				'label' => __( 'Link Text Color', 'listify' ),
				'type' => 'WP_Customize_Color_Control',
			),
			'color-body-text' => array(
				'label' => __( 'Body Text Color', 'listify' ),
				'type'    => 'WP_Customize_Color_Control'
			),
			'color-primary' => array(
				'label' => __( 'Primary Color', 'listify' ),
				'type'    => 'WP_Customize_Color_Control'
			),
			'color-accent' => array(
				'label' => __( 'Accent Color', 'listify' ),
				'type'    => 'WP_Customize_Color_Control'
			),
			'color-as-seen-on-background' => array(
				'label' => __( '"As Seen On" Background Color', 'listify' ),
				'type' => 'WP_Customize_Color_Control'
			),
			'color-footer-widgets-background' => array(
				'label' => __( 'Footer Widgets Background Color', 'listify' ),
				'type' => 'WP_Customize_Color_Control'
			),
			'color-footer-background' => array(
				'label' => __( 'Footer Background Color', 'lisify' ),
				'type' => 'WP_Customize_Color_Control'
			)
		);

		return $this->controls;
	}
	
	public function customizer_scripts() {
		wp_enqueue_script( 'listify-customizer-admin', get_template_directory_uri() . '/js/source/customizer-admin.js', array( 'jquery' ), time(), true );
	}

}
$GLOBALS[ 'listify_customizer_colors' ] = new Listify_Customizer_Controls_Colors();
