<?php
/**
 * AppBuddy_Customizer class.
 *
 * Add extra settings to theme customizer for AppBuddy
 * @since  0.1.0
 */
 
class AppBuddy_Customizer {


	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->hooks();
	}


	/**
	 * hooks function.
	 * 
	 * @access public
	 * @return void
	 */
	public function hooks() {
		add_action( 'customize_register', array( $this, 'add_theme_mod' ) );
	}


	/**
	 * add_theme_mod function.
	 * 
	 * @access public
	 * @param mixed $wp_customize
	 * @return void
	 */
	public function add_theme_mod( $wp_customize ) {
	 
		$theme_name = appp_get_setting( 'appp_theme' )
			? appp_get_setting( 'appp_theme' )
			: null;
		$theme = wp_get_theme( $theme_name );
	 
		$is_app_theme = 0 === strcasecmp( $theme->get_template(), 'AppPresser' ) || 0 === strcasecmp( $theme->get_template(), 'AppTheme' );
	 
		if ( ! $is_app_theme )
			return;
			
		$wp_customize->add_section(
	        'appbuddy_section',
	        array(
	            'title' => 'AppBuddy Settings',
	            'description' => 'Customize AppBuddy',
	            'priority' => 99,
	            'capability' => 'edit_theme_options',
	        )
	    );
		
		// login screen background color
		$wp_customize->add_setting( 
			'ab_color_mod', array(
				'default' => '#eee',
				'capability' => 'edit_theme_options',
		));
		// Controls
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'ab_color_mod',
				array(
					'label' => __( 'Login Screen Background Color', 'appbuddy' ),
					'section' => 'appbuddy_section',
					'settings' => 'ab_color_mod',
				)
			)
		);
		
		// add login screen image
		$wp_customize->add_setting( 'ab_image_mod', array(
			'default' => '',
			'capability' => 'edit_theme_options',
		) );
		// Controls
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'ab_image_mod',
				array(
					'label' => __( 'Login Screen Background Image', 'appbuddy' ),
					'section' => 'appbuddy_section',
					'settings' => 'ab_image_mod',
				)
			)
		);
		
		// add login screen text
		$wp_customize->add_setting( 'ab_text_mod', array(
			'default' => '',
			'capability' => 'edit_theme_options',
		) );
		// Controls
		$wp_customize->add_control(
			'ab_text_mod',
			    array(
			        'label' => 'Login Screen Text',
			        'section' => 'appbuddy_section',
			        'type' => 'text',
			    )
		);				
	 
	}

}
$AppBuddy_Customizer = new AppBuddy_Customizer();


/**
 * appbuddy_customize_css function.
 * 
 * @access public
 * @return void
 */
function appbuddy_customize_css() {
?>
	 <style type="text/css">
	     body.login-modal { 
	     	background-image: url(<?php echo get_theme_mod('ab_image_mod'); ?>); 
	     	background-color: <?php echo get_theme_mod('ab_color_mod'); ?>;
	     	background-repeat: no-repeat;
	     	background-size: cover;
	     }
	 </style>
<?php
}
add_action( 'wp_head', 'appbuddy_customize_css');