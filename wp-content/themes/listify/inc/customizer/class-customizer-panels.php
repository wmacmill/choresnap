<?php

class Listify_Customizer_Panels {

	public function __construct() {
		$this->priority = new Listify_Customizer_Priority(0, 10);

		add_action( 'customize_register', array( $this, 'register_panels' ), 9 );
		add_action( 'customize_register', array( $this, 'organize_appearance' ), 11 );
		add_action( 'customize_register', array( $this, 'organize_general' ), 11 );
	}

	public function panel_list() {
		$this->panels = apply_filters( 'listify_customizer_panels', array(
			'general' => array(
				'title' => __( 'General', 'listify' ),
				'sections' => array()
			),
			'appearance' => array(
				'title' => __( 'Appearance', 'listify' ),
				'sections' => array(
					'colors' => array(
						'title' => __( 'Colors & Scheme', 'listify' ),
						'description' => __( 'Choose a color scheme and continue to
						customize based on your brand. Certain schemes use the
						colors differently so be sure to experiment!', 'listify' ),
					),
					'nav' => array(
						'title' => __( 'Menus & Navigation', 'listify' )
					),
					'header_image' => array(
						'title' => __( 'Header & Logo', 'listify' ),
					)
				)
			),
			'footer' => array(
				'title' => __( 'Footer Area', 'listify' ),
				'sections' => array(
					'call-to-action' => array(
						'title' => __( 'Call to Action', 'listify' )
					),
					'as-seen-on' => array(
						'title' => __( 'As Seen On', 'listify' )
					),
					'copyright' => array(
						'title' => __( 'Copyright', 'listify' )
					)
				)
			)
		) );

		return $this->panels;
	}

	public function register_panels( $wp_customize ) {
		$panels = $this->panel_list();

		foreach ( $panels as $key => $panel ) {
			$defaults = array(
				'priority' => $this->priority->next()
			);

			$panel = wp_parse_args( $defaults, $panel );

			$wp_customize->add_panel( $key, $panel );

			$sections = isset( $panel[ 'sections' ] ) ? $panel[ 'sections' ] : false;

			if ( $sections ) {
				$this->add_sections( $key, $sections, $wp_customize );
			}
		}
	}

	public function add_sections( $panel, $sections, $wp_customize ) {
		$priority = new Listify_Customizer_Priority();

		foreach ( $sections as $key => $section ) {
			$wp_customize->add_section( $key, array(
				'title' => $section[ 'title' ],
				'panel' => $panel,
				'priority' => $priority->next(),
				'description' => isset( $section[ 'description' ] ) ? $section[
				'description' ] : ''
			) );

			$file = dirname( __FILE__ ) . '/controls/class-controls-' . $key . '.php';

			if ( file_exists( $file ) ) {
				require_once( $file );
			}
		}
	}

	public function organize_appearance( $wp_customize ) {
		$wp_customize->get_section( 'colors' )->panel = 'appearance';
		$wp_customize->get_section( 'header_image' )->panel = 'appearance';
		$wp_customize->get_section( 'background_image' )->panel = 'appearance';

		$wp_customize->get_control( 'blogname' )->section = 'header_image';
		$wp_customize->get_control( 'display_header_text' )->section = 'header_image';

		$wp_customize->get_section( 'nav' )->panel = 'appearance';
		$wp_customize->get_section( 'nav' )->priority = 0;

		return $wp_customize;
	}

	public function organize_general( $wp_customize ) {
		$wp_customize->get_section( 'static_front_page' )->panel = 'general';
		$wp_customize->get_section( 'static_front_page' )->title = __( 'Homepage Display', 'listify' );

		$wp_customize->remove_control( 'blogdescription' );

		return $wp_customize;
	}

}
