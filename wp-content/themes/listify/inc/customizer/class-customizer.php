<?php
/**
 * Customize
 *
 * @package Listify
 * @since Listify 1.0.0
 */

class Listify_Customizer {

	public function __construct() {
		$files = array(
			'helper-functions.php',
			'class-customizer-priority.php',
			'class-customizer-panels.php',
			'class-customizer-controls.php',
			'class-customizer-css.php',
		);

		foreach ( $files as $file ) {
			include_once( trailingslashit( dirname( __FILE__) ) . $file );
		}
		
		add_action( 'customize_register', array( $this, 'custom_controls' ) );
		add_action( 'wp_loaded', array( $this, 'setup_panels' ), 9 );

		$output = array(
			'class-customizer-output-colors.php',
			'class-customizer-output-marker-appearance.php',
			'class-customizer-output-as-seen-on.php'
		);

		foreach ( $output as $file ) {
			include_once( trailingslashit( dirname( __FILE__) ) . 'output/' . $file );
		}
	}

	public function custom_controls() {
		include_once( dirname( __FILE__) . '/control/class-control-textarea.php' );
		include_once( dirname( __FILE__) . '/control/class-control-color-schemes.php' );
	}

	public function setup_panels() {
		$this->panels = new Listify_Customizer_Panels();
	}

}

$GLOBALS[ 'listify_customizer' ] = new Listify_Customizer;
