<?php

class Listify_WP_Job_Manager_Service_OpenTable extends Listify_WP_Job_Manager_Service {

	public function __construct() {
		$this->meta_key = 'opentable';
		$this->label    = __( 'Book with OpenTable', 'listify' );

		parent::__construct();
	}

	public function get_content() {
		return sprintf( '<script type="text/javascript" src="https://secure.opentable.com/frontdoor/default.aspx?rid=%1$s&restref=%1$s&bgcolor=F6F6F3&titlecolor=0F0F0F&subtitlecolor=0F0F0F&btnbgimage=https://secure.opentable.com/frontdoor/img/ot_btn_red.png&otlink=FFFFFF&icon=dark&mode=short&hover=1"></script>', $this->get_value() );
	}

}

new Listify_WP_Job_Manager_Service_OpenTable;