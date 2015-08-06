<?php

class Listify_Customizer_Priority {

	public $priority;

	public function __construct( $start = 0, $inc = 1 ) {
		$this->start = $start;
		$this->inc = $inc;

		$this->priority = $start;

		$this->started = false;

		return $this->priority;
	}

	public function next() {
		$this->priority = $this->priority + $this->inc;

		return $this->priority;  
	}

	public function reset() {
		return $this->priority = $start;
	}

}
