<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Main extends MY_Controller {
    
    public function index() {
        $this->load->view('header.tpl');
        $this->load->view('hello');
        $this->load->view('footer.tpl');
    }
}