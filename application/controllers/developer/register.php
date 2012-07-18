<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * 
 * Register a new client for this application's Oauth2.0 server
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends CI_Controller {
    
    function index() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        // check if signed in
        if (!$user = $this->session->user_id) {
        	$this->session->redirect = 'developer/register';
            redirect('authenticate');
        }
        
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('redirect_uri', 'Redirect url', 'required|prep_url');
        
        if ($this->form_validation->run()) {
            $data = new stdClass();
            $data->name = $this->input->post('name');
            $data->redirect_uri = $this->input->post('redirect_uri');
            
            $this->load->model('client_model');
            $client = $this->client_model->create($data->name, $data->redirect_uri, $this->session->user_id);
            redirect('developer/apps/edit/' . $client->client_id);
        
        } else {
            $this->load->view('header.tpl');
            $this->load->view('developer/register');
            $this->load->view('footer.tpl');
        }
    
    }

}
    
