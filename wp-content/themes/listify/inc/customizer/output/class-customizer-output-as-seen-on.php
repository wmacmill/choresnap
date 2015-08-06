<?php

class Listify_Customizer_Output_As_Seen_On {
	
	public function __construct() {
		$this->css = new Listify_Customizer_CSS;

		add_action( 'listify_output_customizer_css', array( $this, 'background' ), 20 );
	}

	public function background() {
		$background = listify_theme_mod( 'color-as-seen-on-background' );

		$this->css->add( array(
			'selectors' => array( '.as-seen-on' ),
			'declarations' => array( 'background-color' => $background )
		) );
	}
}

new Listify_Customizer_Output_As_Seen_On;
