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
        if ($num_segments < $service_name_index + 1) {
            show_json_error('Not enough parameters. Need "api/SERVICE_NAME/ENDPOINT_URI"' );
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
        
        //Check if we have tokens for this service and user
        if(!isset($tokens)){
        	show_json_error("Unable to find credentials for " . $service_name, '403');
        }
        $tokens = reset( $tokens ); // gives first row from array of access_tokens, should be unique
        
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
        } else if ($postbody = file_get_contents('php://input')) {
            show_json_error('plain text postbody not yet supported');
        }
        
        $this->load->driver('service', array('adapter' => $service_name));
        if (!$this->service->is_valid($service_name)) {
            show_json_error($service_name . ' does not exist');
        }
        unset( $params['oauth_token'] );
        $this->service->{$service_name}->set_authentication($tokens);
        
        $output = $this->service->{$service_name}->api($endpoint_uri, $params, $method);
        if(json_decode($output)){
        	header('Content-type: application/json');
        }        	
        echo $output;
        
    }
}