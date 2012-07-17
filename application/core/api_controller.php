<?php

class Api_Controller extends CI_Controller{
    
    protected $auth = NULL ;
    
    function __construct(){
        parent::__construct();
        $this->load->model('access_token_model');
    }
    
    /**
     * @return object ( 'client_id','user_id','access_token')
     */
     
    protected function is_authenticated(){
        // Get token
        $token = $this->input->get_request_header('Authorization');
        
        if (preg_match('/^OAuth\ (.*)$/i', $token, $matches)) {
            // Validate token
            $token = $matches[1];
        }
        
        if (!$token) {
            $token = $this->input->get('oauth_token');
        }
        
        if ($token) {
            $row = $this->access_token_model->is_valid($token);
            if (isset($row->user_id)) {
                $this->auth = $row ;
                return TRUE ;
            }
        }
        
        $this->return_error( array('error'=>'authentication failure'));
        return FALSE ;
    }
    
    /**
     * @param give error object which will be outputted in json format
     * NEEDS RETURN STATEMENT IN CONTROLLER AFTER CALLING THIS
     */
    protected function return_error($error) {
        $this->output->set_content_type('application/json');
        $this->output->set_output( json_encode($error));
    }
}
