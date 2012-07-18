<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Koen De Groote <koen at iRail.be>
 */

class Clients extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        if (!$this->session->user_id) {
            $this->session->redirect = 'clients';
            redirect('authenticate');
        }
    }
    
    function index() {
        $this->load->model('user_model');
        $results = $this->user_model->authorized_clients($this->session->user_id);
        
        $this->load->view('header.tpl');
        $this->load->view('clients', array('results' => $results));
        $this->load->view('footer.tpl');
    }
    
    function edit($client) {
        $this->load->model('client_model');
        $item = $this->client_model->get($client);
        
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('redirect_uri', 'Redirect url', 'required|prep_url');
        
        if ($this->form_validation->run()) {
            if ($this->input->post('clientid') !== $client) {
                //There is a hidden field which must be received. If not, trouble.
                redirect('logout');
            }
            
            //We're resetting the secret
            if ($this->input->post('resetSecret') !== false) {
                //Nothing is actually done with $data
                $this->client_model->reset_secret($client);
                redirect('clients/edit/' . $client);
            }
            
            //We're updating the user
            if ($this->input->post('updateUri') !== false) {
                $name = $this->input->post('name');
                $redirect_uri = $this->input->post('redirect_uri');
                $this->client_model->update($client, $name, $redirect_uri);
                redirect('clients/');
            }
        }
        
        $this->load->view('header.tpl');
        $this->load->view('client', array('item' => $item));
        $this->load->view('footer.tpl');
    }

}