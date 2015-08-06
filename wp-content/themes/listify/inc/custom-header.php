<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package Listify
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses listify_header_style()
 * @uses listify_admin_header_style()
 * @uses listify_admin_header_image()
 *
 * @package Listify
 */
function listify_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'listify_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => 'fff',
		'header-text'            => true,
		'width'                  => 100,
		'height'                 => 35,
		'flex-height'            => true,
		'flex-width'             => true,
		'wp-head-callback'       => 'listify_header_style',
		'admin-head-callback'    => 'listify_admin_header_style',
		'admin-preview-callback' => 'listify_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'listify_custom_header_setup' );

if ( ! function_exists( 'listify_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see listify_custom_header_setup().
 */
function listify_header_style() {
	$header_text_color = get_header_textcolor();
?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		.site-branding .site-title,
		.site-branding .site-description,
		.site-header-minimal .site-title,
		.site-header-minimal .site-description {
			display: none;
		}
	<?php
		endif;
	?>
	.site-title a,
	.site-description {
		color: #<?php echo $header_text_color; ?>;
	}
	</style>
	<?php
}
endif; // listify_header_style

if ( ! function_exists( 'listify_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see listify_custom_header_setup().
 */
function listify_admin_header_style() {
	/* Supplimentary CSS */
	wp_enqueue_style( 'listify-fonts', listify_fonts_url() );

	$header_image = get_custom_header();
?>
	<style type="text/css">
		.appearance_page_custom-header #headimg {
			border: none;
			background-color: <?php echo listify_theme_mod( 'color-header-background' ); ?>;
			padding: 10px;
			width: auto;
		}

		.appearance_page_custom-header #headimg img {
			float: none;
			display: inline-block;
			vertical-align: middle;
		}

		#headimg h1 {
			margin: 0 0 0 20px;
			font-family:  'Montserrat', sans-serif;
			font-size: 26px;
			font-weight: normal;
			display: inline-block;
			vertical-align: middle;
		}

		#headimg h1 a {
			text-decoration: none;
		}

		#desc {
			display: none;
		}

		#headimg img {
			float: left;
		}
	</style>
<?php
}
endif; // listify_admin_header_style

if ( ! function_exists( 'listify_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see listify_custom_header_setup().
 */
function listify_admin_header_image() {
	$style        = sprintf( ' style="color:#%s;"', get_header_textcolor() );
	$header_image = get_header_image();
?>
	<div id="headimg">
		<?php if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="">
		<?php endif; ?>

		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div class="displaying-header-text" id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
<?php
}
endif; // listify_admin_header_image
