<?php

/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Authenticate extends MY_Controller {
    
    private $services = array('facebook', 'twitter', 'google', 'viking', 'foursquare');
    
    function index() {
        if ($user_id = $this->session->user_id) {
            //If coming from register, can't overwrite the session redirect.
            //$this->session->redirect = 'authenticate';
            redirect('profile/plugs');
        }
        
        $plugs = new stdClass();
        foreach ($this->services as $service) {
            $plugs->$service = FALSE;
        }
        
        $data = new stdClass();
        $data->plugs = $plugs;
        
        $this->load->view('header.tpl');
        $this->load->view('profile/plugs', $data);
        $this->load->view('footer.tpl');
    }
}
