<?php

class API_Controller extends CI_Controller{
    
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
        
        if (!$token) {
            $post_params = array();
            parse_str(file_get_contents('php://input'),$post_params);
            $_POST = $post_params ;
            if (isset($post_params['oauth_token'])) {
                $token = $post_params['oauth_token'];
            } else {
                return FALSE;
            }
            
        }
        
        if ($token) {
            $row = $this->access_token_model->is_valid($token);
            if (isset($row->user_id)) {
                $this->auth = $row ;
                return TRUE ;
            }
        }
        return FALSE;
    }
}
