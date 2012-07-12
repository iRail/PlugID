<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class api extends CI_Controller {
    
    function index() {
        $this->load->model('access_token_model');
        $this->load->model('user_model');
        
        $service_name_index = 2;
        
        // at least 3
        $num_segments = $this->uri->total_segments();
        if ($num_segments < 3) {
            $this->return_error(array('error' => 'Not enough parameters. We need "api/SERVICE_NAME/ENDPOINT_URI" .'));
        }
        
        // build endpoint_uri
        $segments = $this->uri->segment_array();
        $endpoint_uri = '';
        for ($i = $service_name_index + 1; $i <= $num_segments; $i += 1) {
            $endpoint_uri .= $segments[$i] . '/';
        }
        $endpoint_uri = rtrim($endpoint_uri, '/');
        
        // get service_name
        $service_name = $segments[$service_name_index];
        
        // get authentication token
        $error = FALSE;
        $tokens = NULL;
        $auth_header = $this->input->get_request_header('Authorization');
        if (!preg_match('/^Bearer\ (.*)$/', $auth_header, $matches)) {
            // no authentication was given
            $error = array('error' => 'no authentication was given');
        } else {
            // validate token
            $row = $this->access_token_model->is_valid($matches[1]);
            if (!isset($row->user_id)) {
                // authentication was tried and failed
                $error = array('error' => 'invalid access_token');
            } else {
                $tokens = $this->user_model->get_tokens($row->user_id, $service_name);
            }
        }
        
        if ($error) {
            $this->return_error($error);
        }
        
        $get_params = $this->input->get();
        $post_params = $this->input->post();
        
        $method = 'get'; // default
        $params = array();
        if ($get_params !== FALSE) {
            $method = 'get';
            $params = $get_params;
        } else if ($post_params !== FALSE) {
            $method = 'post';
            $params = $post_params;
        } else {
            if ($postbody = file_get_contents('php://input')) {
                $this->return_error(array('error' => 'plain postbody not yet supported'));
            }
        }
        
        // Y U NO LOAD?
        $this->load->driver('service', array('adapter' => $service_name));
        if (!$this->service->is_valid($service_name)) {
            $this->return_error(array('error' => $service_name . ' does not exist'));
        }
        
        $this->service->{$service_name}->set_authentication($tokens);
        echo $this->service->{$service_name}->api($endpoint_uri, $params, $method);
    }
    
    private function return_error($error) {
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($error));
        exit();
    }
}