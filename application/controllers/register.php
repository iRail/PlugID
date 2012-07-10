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
        
        // check if signed in
        $user = $this->session->user;
        
        // it's a no go
        if( $user === FALSE ){
            redirect('/login');
        }
        
        // logged in
        
        // trying to register
        if( $this->input->post('register') !== false ){
            $data->name = $this->input->post('name');
            $data->redirect_uri = $this->input->post('redirect_uri');
            $is_url = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $data->redirect_uri);
            
            if( !($data->name && $data->redirect_uri) || !$is_url ){
                $this->load->view('register',$data);
            }else{
                $this->load->model('client_model');
                $client = $this->client_model->create( $data->name, $data->redirect_uri, $this->session->user );
                redirect('/consumer/' . $client->client_id );
            }
        }else{
            // show registration page
            $this->load->view('register');
        }
        
    }

}
    
