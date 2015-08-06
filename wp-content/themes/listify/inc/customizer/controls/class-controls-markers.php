<?php

class Listify_Customizer_Section_Markers {

	public function __construct() {
		$this->priority = new Listify_Customizer_Priority();

		add_action( 'customize_register', array( $this, 'add_controls' ), 10 );
	}

	public function add_controls( $wp_customize ) {
		$this->controls = array( 
			'test' => array(
				'label'   => __( 'Title', 'listify' ),
				'type'    => 'text',
			)
		);

		wp_die( 'wat' );
		foreach ( $this->controls as $key => $control ) {
			$wp_customize->add_setting( $key, array(
				'default' => ''
			) );

			$wp_customize->add_control( $key, array(
				'label' => $control[ 'label' ],
				'section' => 'marker-apperance',
				'settings' => $key,
				'priority' => $this->priority->next(),
				'type' => $control[ 'type' ]
			) );
		}

		return $wp_customize;
	}

}

new Listify_Customizer_Section_Markers();
