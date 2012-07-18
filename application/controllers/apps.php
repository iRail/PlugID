<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Apps extends CI_Controller {
    
    function index() {
        $this->load->model('user_model');
        
        if (!$user_id = $this->session->user_id) {
            redirect('');
        }
        
        if ($revoke_client = $this->input->post('revoke')) {
            $this->user_model->revoke( $user_id, $revoke_client );
        }
        
        $clients = $this->user_model->authorized_clients($user_id);
        $data->clients = $clients;
        
        $this->load->view('header.tpl');
        $this->load->view('apps',$data);
        $this->load->view('footer.tpl');
    }
    
}