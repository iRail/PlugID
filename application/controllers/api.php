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
        
        $service_name_index = 2 ;
        
        // at least 3
        $num_segments = $this->uri->total_segments();
        if( $num_segments < 3 ){
            show_error( 'Not enough parameters. We need "api/SERVICE_NAME/ENDPOINT_URI" .' );
        }
        
        // build endpoint_uri
        $segments = $this->uri->segment_array();
        $endpoint_uri = '';
        for( $i = $service_name_index + 1 ; $i <= $num_segments ; $i += 1 ){
            $endpoint_uri .= $segments[$i] . '/' ;
        }
        $endpoint_uri = rtrim($endpoint_uri,'/');
        
        // get service_name
        $service_name = $segments[$service_name_index] ;
        
        // get authentication token
        $error = FALSE ;
        $tokens = NULL ;
        $auth_header = $this->input->get_request_header('Authorization');
        if( preg_match('/^Bearer\ (.*)$/', $auth_header, $matches)){
            
            // validate token
            $row = $this->access_token_model->is_valid( $matches[1] );
            if( count( $row ) > 0 && $this->user_model->is_client_authorized( $row['user_id'], $row['client_id'] )){
                $tokens = $this->user_model->get_tokens($row->user_id, $service_name);
            }else{
                // authentication was tried and failed
                $error = array('error'=>'');
            }
        }else{
            // no authentication was given
            $error = array('error'=>'');
        }
        
        if( $error ){
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode( $error ));
            exit;
        }
        
        var_dump( $tokens ); exit ;
        
        $get_params  = $this->input->get();
        $post_params = $this->input->post();
        
        if( $get_params !== FALSE ){
            $method = 'get';
        }else if( $post_params !== FALSE ){
            $method = 'post';
        }
    }
}