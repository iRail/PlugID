<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Koen De Groote <koen at iRail.be>
 */

class Apps extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        if (!$this->session->user_id) {
            $this->session->redirect = 'profile/apps';
            redirect('authenticate');
        }
    }
    
    function index() {
        $this->load->model('user_model');
        $results = $this->user_model->authorized_clients($this->session->user_id);
        
        $this->load->view('header.tpl');
        $this->load->view('profile/apps', array('results' => $results));
        $this->load->view('footer.tpl');
    }

}