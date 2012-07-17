<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Koen De Groote <koen at iRail.be>
     */
    class Edit extends CI_Controller {

        // Declaring false prevents warning
        function index($clientId = false) {
            if ($user_id = $this->session->user_id) {
                //In both cases, we need it. So load it now
                $this->load->model('user_model');
                $results['results'] = $this->user_model->get_clients($user_id);
                $results['version'] = 'view';

                //Is this a single view?
                if ($clientId !== false) {
                    //It's not, so display the application data
                    $results['version'] = 'edit';
                    
                    //Get the specific client
                    $this->load->model('client_model');
                    $item = $this->client_model->get($clientId);
                    
                    $this->load->helper('url');
                    $this->load->helper('form');
                    $this->load->library('form_validation');

                    $this->form_validation->set_rules('name', 'Name', 'required');
                    $this->form_validation->set_rules('redirect_uri', 'Redirect url', 'required|prep_url');
                    $this->form_validation->set_rules('client_secret', 'ClientSecret', 'required');
                    $this->form_validation->set_rules('client_id', 'ClientId', 'required');

                    if ($this->form_validation->run()) {
                        $data = new stdClass();
                        $data->name = $this->input->post('name');
                        $data->redirect_uri = $this->input->post('redirect_uri');
                        $data->client_secret = $this->input->post('client_secret');
                    }
                    $results['item'] = $item;
                }

                //Get all the clients this user has.
                $this->load->view('header.tpl');
                $this->load->view('edit', $results);
                $this->load->view('footer.tpl');
            } else {
                redirect('');
            }
        }

    }

?>