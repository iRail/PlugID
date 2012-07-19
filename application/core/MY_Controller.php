<?php

class MY_Controller extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		
		// enable csrf protection
		$this->config->set_item('csrf_protection', TRUE);
		
		// verify input
		$this->security->csrf_verify();
	}
	
}