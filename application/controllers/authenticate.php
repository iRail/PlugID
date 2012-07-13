<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Jens Segers <jens at iRail.be>
     * @author Hannes Van De Vreken <hannes at iRail.be>
     */
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class Authenticate extends CI_Controller {

        function index() {
            if ($user_id = $this->session->user) {
                //Load user model
                $this->load->model('user_model');
                //Retrieve all services this user has tokens for. ie, is using.
                $services_types = array("foursquare", "facebook", "twitter", "viking");
                $service_have = array();
                foreach ($services_types as $service) {
                    //get_object_vars expects array. If not present, will not return array. Error suppresion shorter than if/else
                    $linked = @get_object_vars($this->user_model->get_tokens($user_id, $service));
                    if ($linked['service_type'] != null) {
                        array_push($service_have, $linked['service_type']);
                    }
                }
                
                $data['services'] = $service_have;
                $this->load->view('authenticate', $data);
            } else {
                $this->load->view('authenticate');
            }
        }
    }
