<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Koen De Groote <koen at iRail.be>
     */
    class Consumer extends CI_Controller {

        // Declaring false prevents warning
        function index($clientId = false) {
            if ($user_id = $this->session->user_id) {
                //In both cases, we need it. So load it now
                $this->load->model('user_model');
                $results['results'] = $this->user_model->authorized_clients($user_id);
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
                        if($this->input->post('clientid') === false) {
                            //There is a hidden field which must be received. If not, trouble.
                            redirect('logout');
                        }
                        //We're resetting the secret
                        if($this->input->post('resetSecret') !== false) {
                            $client_id = $this->input->post('clientid');
                            $data = $this->client_model->reset_secret($client_id);
                            redirect('consumer/'.$client_id);
                        }
                        //We're updating the user
                        if($this->input->post('updateUri') !== false) {
                            $client_id = $this->input->post('clientid');
                            $name = $this->input->post('name');
                            $redirect_uri = $this->input->post('redirect_uri');
                            $this->client_model->update($client_id,$name,$redirect_uri);
                            redirect('consumer/'.$this->input->post('clientid'));
                        }
                    }
                    $results['item'] = $item;
                }


                //Get all the clients this user has.
                $this->load->view('header.tpl');
                $this->load->view('consumer', $results);
                $this->load->view('footer.tpl');
            } else {
                redirect('');
            }
        }

    }

?>