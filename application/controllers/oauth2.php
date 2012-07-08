<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class oauth2 extends CI_Controller {
    
    function index() {
        redirect('register');
    }
    
    function authorize() {
        /* pre-checks:
    	 * - user logged in yet?
    	 * - token for this user?
    	 * - ...
    	 */
    	// check if signed in
        
        // required
        $client_id = $this->input->get('client_id');
        $response_type = $this->input->get('response_type');
        
        // optional
        $redirect_uri = $this->input->get('redirect_uri');
        $state = $this->input->get('state');
        
        // invalid_request
        if (!($client_id && $response_type)) {
            show_error('invalid_request');
        }
        
        // unsupported_response_type
        if ($response_type != 'code') {
            show_error('unsupported_response_type');
        }
        
        // check on client
        $this->load->model('client_model');
        $client = $this->client_model->get($client_id);
        
        // client does not exist
        if (!$client) {
            show_error('unauthorized_client');
        }
        
        // optional callback
        $redirect_uri = $redirect_uri ? $redirect_uri : $client->redirect_uri;
        
        // check if user is actually signed in
        $user = $this->session->user;
        
        // it's a no go
        if( $user === FALSE ){
            // save request data to return later on
            $this->session->auth_request = new stdClass();
            $auth_request = &$this->session->auth_request ;
            
            $auth_request->client_id = $this->input->get('client_id');
            $auth_request->response_type = $this->input->get('response_type');
            
            $auth_request->redirect_uri = $redirect_uri;
            $auth_request->state = $this->input->get('state');
            // get the user to log in
            redirect('login');
        }
        
        // allow button clicked
        if ($this->input->post('allow')) {
            // generate code
            $code = md5(time() . uniqid());
            
            $this->load->model('code_model');
            $this->code_model->insert($client_id, $user, $redirect_uri, $code, 600); // 10 minutes
            
            // generate callback url
            $redirect_uri = $redirect_uri . '?' . http_build_query(array('code' => $code, 'state' => $state));
            
            // redirect back to user website
            redirect($redirect_uri);
            
        } else {
            
            // show access screen
            $this->load->view('authenticate', array('client' => $client->name));
        }
    }
    
    function access_token() {
        $this->load->library('OAuth2_server');
        $data = array();
        
        // required
        $grant_type = $this->input->post('grant_type');
        $code = $this->input->post('code');
        $redirect_uri = $this->input->post('redirect_uri');
        
        // hard-coded: 'grant-type' must be 'authorization_code'
        if( $grant_type != 'authorization_code' ){
            $data['error'] = 'invalid_request' ;
        }else{
            $data['access_token'] = '' ;
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode( $data ));
    }

}
