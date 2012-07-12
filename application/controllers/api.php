<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
 
include 'application/core/api_controller.php' ;

class Api extends Api_Controller {
    
    function index() {
        $this->load->model('user_model');
        
        $service_name_index = 2;
        
        // at least 3
        $num_segments = $this->uri->total_segments();
        if ($num_segments < 3) {
            return $this->return_error(array('error' => 'Not enough parameters. We need "api/SERVICE_NAME/ENDPOINT_URI" .'));
        }
        
        // Get segments
        $segments = $this->uri->segment_array();
        $service_name = $segments[$service_name_index];
        
        // Build uri
        $endpoint_uri = '';
        for ($i = $service_name_index + 1; $i <= $num_segments; $i += 1) {
            $endpoint_uri .= $segments[$i] . '/';
        }
        $endpoint_uri = rtrim($endpoint_uri, '/');
        
        // authenticate
        if (!$this->is_authenticated()) {
            return FALSE;
        }
        
        // load tokens for service
        $tokens = $this->user_model->get_tokens($this->auth->user_id, $service_name);
        
        $get_params = $this->input->get();
        $post_params = $this->input->post();
        
        $method = 'get'; // default
        $params = array();
        if ($get_params !== FALSE) {
            //$method = 'get'; // is default anyway
            $params = $get_params;
        } else if ($post_params !== FALSE) {
            $method = 'post';
            $params = $post_params;
        } else {
            if ($postbody = file_get_contents('php://input')) {
                return $this->return_error(array('error' => 'plain text postbody not yet supported'));
            }
        }
        
        // Y U NO LOAD?
        $this->load->driver('service', array('adapter' => $service_name));
        if (!$this->service->is_valid($service_name)) {
            return $this->return_error(array('error' => $service_name . ' does not exist'));
        }
        
        $this->service->{$service_name}->set_authentication($tokens);
        echo $this->service->{$service_name}->api($endpoint_uri, $params, $method);
    }
}