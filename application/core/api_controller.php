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
        $auth_header = $this->input->get_request_header('Authorization');
        if (preg_match('/^Bearer\ (.*)$/', $auth_header, $matches)) {
            // Validate token
            $row = $this->access_token_model->is_valid($matches[1]);
            if (isset($row->user_id)) {
                $this->auth = $row ;
                return TRUE ;
            }
        }
        $this->return_error( array('error'=>'authentication failure'));
        return FALSE ;
    }
    
    protected function return_error($error) {
        $this->output->set_content_type('application/json');
        $this->output->set_output( json_encode($error));
    }
}