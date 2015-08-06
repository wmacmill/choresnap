<?php
/**
 * Jetpack
 */

class Listify_Jetpack extends listify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-jetpack-share.php'
		);

		$this->integration = 'jetpack';

		parent::__construct();
	}

	public function setup_actions() {

	}
}

$GLOBALS[ 'listify_jetpack' ] = new Listify_Jetpack();