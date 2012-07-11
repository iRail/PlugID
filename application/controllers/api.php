<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class api extends CI_Controller {
    
    private $ci ;
    
    function index() {
        $this->ci = &get_instance();
        
        $service_name_index = 2 ;
        
        // at least 3
        $num_segments = $this->ci->uri->total_segments();
        if( $num_segments < 3 ){
            show_error( 'Not enough parameters. We need "api/SERVICE_NAME/ENDPOINT_URI" .' );
        }
        
        // build endpoint_uri
        $segments = $this->ci->uri->segment_array();
        $endpoint_uri = '';
        for( $i = $service_name_index + 1 ; $i <= $num_segments ; $i += 1 ){
            $endpoint_uri .= $segments[$i] . '/' ;
        }
        $endpoint_uri = rtrim($endpoint_uri,'/');
        
        // get service_name
        $service_name = $segments[$service_name_index] ;
        
        // get all parameters
        $get_params  = $this->input->get();
        $post_params = $this->input->post();
        
        // get authentication token
        $oauth_token = $this->input->get('oauth_token');
        $oauth_token = $oauth_token ? $oauth_token : $this->input->post('oauth_token');
        $oauth_token = $oauth_token ? $oauth_token : $this->input->get_request_header('Authorization');
        
        if( $get_params !== FALSE ){
            
        }else if( $post_params !== FALSE ){
            
        }
    }
}