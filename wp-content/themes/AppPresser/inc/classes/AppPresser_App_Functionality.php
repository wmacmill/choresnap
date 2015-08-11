<?php
/**
 * AppPresser App Functionality
 *
 * @package AppPresser Theme
 * @since   0.0.1
 */

/**
 * Theme hooks
 */

// Hooks into right side of top toolbar in app panel
function appp_app_panel_menu() {
	 do_action( 'appp_app_panel_menu' );
}

// Left panel top, used for search bar, shopping cart, and user profile pic
function appp_left_panel_before() {
	 do_action( 'appp_left_panel_before' );
}


class AppPresser_App_Functionality {

	/**
	 * AppPresser_App_Functionality hooks
	 * @since 1.0.6
	 */
	public function hooks() {
		return array(
			// Add app panel before #main
			// array( 'wp_footer', 'app_panel' ),
			array( 'wp_footer', 'modal_template' ),
			array( 'wp_footer', 'login_modal_template' ),
			// Hook menu into app panel
			array( 'appp_app_panel_menu', 'panel_menu' ),
			// Add Search Bar to left panel menu
			array( 'appp_left_panel_before', 'left_panel_search', 20 ),
			// Custom login page styling
			array( 'login_enqueue_scripts', 'custom_login_styles' ),
		);
	}

	/**
	 * Add app panel before #main
	 * @since  0.0.1
	 */
	function app_panel() {
		?>
	<aside class="app-panel">
		<header class="toolbar">
			<a href="#" class="btn back-btn"><i class="ion ion-chevron-left"></i> <?php _e('Back', 'apptheme'); ?></a>
			<?php appp_app_panel_menu(); ?>
		</header>
		<section class="item-content">
		</section>
	</aside>
		<?php
	}

	/**
	 * Modal's html markup
	 * @since  0.0.1
	 */
	function modal_template() {
		?>
	<aside class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<div class="modal-inside"></div>
		</div>
	</aside>
		<?php
	}

	/**
	 * login modal html markup
	 * @since  0.0.1
	 */
	function login_modal_template() {
		?>
	<aside class="io-modal" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="toolbar site-header">
			<i class="io-modal-close fa fa-times fa-lg alignright"></i>
		</div>
		<div class="io-modal-content">
			<?php if ( !is_user_logged_in() ) {
				echo '<h2 class="login-modal-title">' . __( 'Please Login', 'apptheme' ) . '</h2>';
					wp_login_form();
				} else {
					_e( 'Welcome back!', 'apptheme' );
				}
			?>
		</div>
	</aside>
		<?php
	}

	/**
	 * Hook menu into app panel
	 * @since  0.0.1
	 */
	function panel_menu() {

		if ( ! has_nav_menu( 'panel-menu' ) )
			return;

		$icon = get_theme_mod( 'panel_menu_icon' );
		$icon = $icon ? $icon : 'fa fa-cog'; // fallback

		?>
		<nav id="app-panel-menu" class="top-menu pull-right" role="navigation">
		<a class="nav-right-btn dropdown-toggle" data-toggle="dropdown">
			<i class="<?php echo $icon; ?> fa-lg"></i>
		</a>
		<?php
			wp_nav_menu( array(
				'theme_location' => 'panel-menu',
				'container_class' => 'dropdown-menu',
			) );
		?>
		</nav>
		<?php
	}

	/**
	 * Add Search Bar to left panel menu
	 * @since  0.0.1
	 */
	function left_panel_search() {
		?>
	<div class="shelf-search">
		<i class="search-icon"></i>
		<form method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
		<label for="s" class="screen-reader-text"><?php _ex( 'Search', 'assistive text', 'apptheme' ); ?></label>
		<input type="search" class="field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'apptheme' ); ?>" />
		</form>
	</div>
		<?php
	}

	/**
	 * Custom login page styling
	 * @since  0.0.1
	 */
	function custom_login_styles() {
		?>
		<style type="text/css">
		body.login div#login h1 a {
			background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/images/login-logo.png);
			padding-bottom: 40px;
			background-size: 90%;
		}
		body.login {
			background: #eee;
		}
		.login form {
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			border: none;
			background: #fff;
		}
		.login form .input, .login input[type="text"] {
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			border: none;
			background: #eee !important;
		}
		.login form input[type="submit"], #registerform input[type="submit"] {
			width: 50%;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			border: none;
			background: #278ab7;
			background-image: none;
		}
		#registerform input[type="submit"], #lostpasswordform input[type="submit"] {
			width: 100%;
			clear: both;
		}
		.login form input[type="submit"]:hover, #registerform input[type="submit"]:hover {
			background: #36a1d2;
		}
		.login #nav, .login #backtoblog {
			text-align: center;
		}
		.forgetmenot {
			position: relative;
			top: 5px;
		}
		</style>
		<?php
	}

}
