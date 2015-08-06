<?php

class Listify_Customizer_Controls_Marker_Appearance extends Listify_Customizer_Controls {

	public function __construct() {
		$this->section = 'marker-appearance';
		
		$taxonomy = listify_get_top_level_taxonomy();	
		$this->terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );

		if ( is_wp_error( $this->terms ) ) {
			return;
		}

		$this->icons = $this->get_icon_list();

		parent::__construct();

		add_action( 'customize_register', array( $this, 'add_controls' ), 30 );
		add_action( 'customize_register', array( $this, 'set_controls' ), 35 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array();
		
		foreach ( $this->terms as $term ) {
			$this->controls[ 'marker-icon-' . $term->term_id ] = array(
				'label' => sprintf( __( '%s Icon Class', 'listify' ),
				$term->name )
			);

			$this->controls[ 'marker-color-' . $term->term_id ] = array(
				'label' => sprintf( __( '%s Marker Color', 'listify' ),
				$term->name ),
				'type' => 'WP_Customize_Color_Control'
			);
		}

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

new Listify_Customizer_Controls_Marker_Appearance();
