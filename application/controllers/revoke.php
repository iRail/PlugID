<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Jens Segers <jens at iRail.be>
     * @author Hannes Van De Vreken <hannes at iRail.be>
     * @author Koen De Groote <koen at iRail.be>
     * 
     * Register a new client for this application's Oauth2.0 server
     */
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class Revoke extends CI_Controller {

        function index() {
            // check if signed in. Can't revoke access if not logged in.
            if (!$user_id = $this->session->user_id) {
                redirect('authenticate');
            }

            //Load user model
            $this->load->model('user_model');

            $clients = $this->user_model->authorized_clients($user_id);


            $client_id = $this->input->post('revoke');
            //0 if not clicked. Client_id if clicked
            if ($client_id != 0) {
                //Revoke access, should work once auth_* tables get content
                $this->user_model->revoke($user_id, $client_id);
                redirect('revoke');
            } else {
                // show access screen
                $data = array();
                $clientinfo = array();
                foreach ($clients as $client) {
                    $vars = get_object_vars($client);
                    array_push($data, $vars);
                }
                $clientinfo['multi'] = $data;
                $this->load->view('header.tpl');
                $this->load->view('revoke', $clientinfo);
                $this->load->view('footer.tpl');
            }
        }
    }
?>