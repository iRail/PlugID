<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Plugs extends MY_Controller {
    
    private $services = array('facebook', 'twitter', 'google', 'viking', 'foursquare', 'instagram');
    
    function index() {
        $this->load->model('user_model');
        if (!$user_id = $this->session->userdata('user_id')) {
            $this->session->set_userdata('redirect','profile/plugs');
            redirect('authenticate');
        }
        
        $tokens = $this->user_model->get_tokens($user_id);
        
        $plugs = new stdClass();
        foreach ($this->services as $service) {
            $plugs->$service = FALSE;
            foreach ($tokens as $token) {
                if ($token->service_type == $service) {
                    $plugs->$service = $token;
                }
            }
        }
        
        $this->load->view('header.tpl');
        $this->load->view('profile/plugs', array('plugs' => $plugs));
        $this->load->view('footer.tpl');
    }
}