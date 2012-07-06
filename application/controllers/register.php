<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * 
 * Register a new client for this application's Oauth2.0 server
 */

class register extends CI_Controller {

    function index(){
        $this->load->helper('url');
        $this->load->library('OAuth2_server');
        
        // check if signed in
        $user = $this->session->user;
        
        // it's a no go
        if( $user === FALSE ){
            $this->load->view('login');
            return ;
        }
        
        // logged in
        
        // trying to register
        if( $this->input->post('register') !== false ){
            $data->name = $this->input->post('name');
            $data->callback = $this->input->post('callback');
            $is_url = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $callback);
            
            if( !($data->name && $data->callback) || !$is_url ){
                $this->load->view('register',$data);
            }else{
                $this->OAuth2_server->new_client( $data );
                $this->load->view('consumers');
            }
        }else{
            // show registration page
            $this->load->view('register');
        }
        
    }

}
    
