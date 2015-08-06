<?php

class Listify_Activation {

	public function __construct() {
		$this->theme = wp_get_theme( 'listify' );

		if ( ! isset( $this->theme->Version ) ) {
			$this->theme = wp_get_theme();
		}

		$this->version = get_option( 'listify_version' );

		if ( version_compare( $this->version, $this->theme->Version, '<' ) ) {
			$call = str_replace( '.', '', $this->theme->Version );
			$this->upgrade( $call );
		}

		add_action( 'add_option_job_manager_installed_terms', array( $this, 'enable_categories' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ), 10, 2 );
	}

	public function upgrade( $run ) {
		$upgrade = 'upgrade_' . $run;
	
		if ( is_callable( $upgrade ) ) {
			$this->$upgrade();
		}
		
		$this->set_version();
	}

	public function after_switch_theme( $theme, $old ) {
		$this->flush_rules();

		// If it's set just update version can cut out
		if ( get_option( 'listify_version' ) ) {
			$this->set_version();

			return;
		}

		$this->set_version();
		$this->enable_categories();
		$this->redirect();
	}

	public function set_version() {
		update_option( 'listify_version', $this->theme->version );
	}

	public function flush_rules() {
		flush_rewrite_rules();
	}

	public function enable_categories() {
		update_option( 'job_manager_enable_categories', 1 );
	}

	public function redirect() {
		unset( $_GET[ 'action' ] );

		wp_safe_redirect( admin_url( 'themes.php?page=listify-setup' ) );

		exit();
	}
}

$GLOBALS[ 'listify_activation' ] = new Listify_Activation();
