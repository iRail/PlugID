<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Koen De Groote <koen at iRail.be>
 * @author Jens Segers <jens at iRail.be>
 */

/**
 * Controller for the apps of a developer
 * Overview and editing of clients
 */
class Apps extends MY_Controller {
    
    function __construct() {
        parent::__construct();

        //If user isn't logged in => redirect to authenticate page
        //Store the redirect URL in the session
        if (!$this->session->userdata('user_id')) {
            $this->session->set_userdata('redirect','developer/apps');
            redirect('authenticate');
        }
    }

    /**
     * Show overview of all the registered apps of the user
     */
    function index() {
        $this->load->model('user_model');
        $results = $this->user_model->get_clients($this->session->userdata('user_id'));
        
        $this->load->view('header.tpl');
        $this->load->view('developer/apps', array('results' => $results));
        $this->load->view('footer.tpl');
    }

    /** Editing one client
     * @param $client Selected client
     */
    function edit($client) {
        $this->load->model('client_model');
        $item = $this->client_model->get($client);
        
        // only edit own clients
        if ($item->user_id != $this->session->userdata('user_id')) {
            redirect('logout');
        }
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('redirect_uri', 'Redirect url', 'required|prep_url');
        
        if ($this->form_validation->run()) {
            //We're resetting the secret
            if ($this->input->post('resetSecret') !== false) {
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
            
            //We're deleting the user
            if ($this->input->post('deleteClient') !== false) {
                $this->client_model->delete($client);
                redirect('developer/apps');
            }
        }
        
        $this->load->view('header.tpl');
        $this->load->view('developer/app', array('item' => $item));
        $this->load->view('footer.tpl');
    }

}