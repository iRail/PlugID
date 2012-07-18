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
            $this->session->redirect = 'developer/apps';
            redirect('authenticate');
        }
    }
    
    function index() {
        $this->load->model('user_model');
        $results = $this->user_model->authorized_clients($this->session->user_id);
        
        $this->load->view('header.tpl');
        $this->load->view('developer/apps', array('results' => $results));
        $this->load->view('footer.tpl');
    }
    
    function edit($client) {
        $this->load->model('client_model');
        $item = $this->client_model->get($client);
        
        // only edit own clients
        if ($item->user_id != $this->session->user_id) {
            redirect('logout');
        }
        
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('redirect_uri', 'Redirect url', 'required|prep_url');
        
        if ($this->form_validation->run()) {
            //We're resetting the secret
            if ($this->input->post('resetSecret') !== false) {
                //Nothing is actually done with $data
                $this->client_model->reset_secret($client);
                redirect('developer/apps/edit/' . $client);
            }
            
            //We're updating the user
            if ($this->input->post('updateUri') !== false) {
                $name = $this->input->post('name');
                $redirect_uri = $this->input->post('redirect_uri');
                $this->client_model->update($client, $name, $redirect_uri);
                redirect('developer/apps');
            }
        }
        
        $this->load->view('header.tpl');
        $this->load->view('developer/app', array('item' => $item));
        $this->load->view('footer.tpl');
    }

}