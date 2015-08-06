<?php

class Listify_Customizer_Controls_Nav extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'nav';
		
		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );

		add_action( 'listify_output_customizer_css', array( $this, 'add_css' ) );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array(
			'nav-cart' => array(
				'label' => __( 'Display cart icon', 'listify' ),
				'type' => 'checkbox'
			),
			'nav-search' => array(
				'label' => __( 'Display search icon', 'listify' ),
				'type' => 'checkbox'
			),
			'nav-megamenu' => array(
				'label' => __( 'Secondary Mega Menu Taxonomy', 'listify' ),
				'type' => 'select',
				'choices' => array_merge( array( 'none' => __( 'None', 'listify' ) ), $this->get_taxonomies() )
			)
		);

		return $wp_customize;
	}

	public function add_css() {
		$css = new Listify_Customizer_CSS();

		$css->add( array(
			'selectors' => array(
				'.primary.nav-menu .current-cart .current-cart-count'
			),
			'declarations' => array(
				'border-color' => listify_theme_mod( 'color-header-background' ) 
			)
		) );
	}
	
	private function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true ), array( 'output' => 'objects' ) );
		$_taxonomies = array();
		foreach ( $taxonomies as $taxonomy ) {
			$_taxonomies[ $taxonomy->name ] = $taxonomy->labels->name;
		}
		return $_taxonomies;
	}
}

new Listify_Customizer_Controls_Nav();
