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
        $user_id = $this->session->user;
        
        // it's a no go
        if ($user_id === FALSE) {
            // save request data to return later on
            $this->session->auth_request = new stdClass();
            $auth_request = &$this->session->auth_request;
            
            $auth_request->client_id = $this->input->get('client_id');
            $auth_request->response_type = $this->input->get('response_type');
            
            $auth_request->redirect_uri = $redirect_uri;
            $auth_request->state = $this->input->get('state');
            // get the user to log in
            redirect('login');
        }
        
        // allow button clicked
        if ($this->input->post('allow')) {
            $this->load->model('code_model');
            $code = $this->code_model->create($client_id, $user_id);
            
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
        $data = array();
        
        // Required
        $grant_type = $this->input->post('grant_type');
        $code = $this->input->post('code');
        $redirect_uri = $this->input->post('redirect_uri');
        $client_id = $this->input->post('client_id');
        
        // Client secret from basic auth header OR post param
        $client_secret = $this->input->get_request_header('Authorization');
        if ($client_secret !== FALSE && preg_match('/^Basic\ (\w{32})$/', $client_secret, $matches)) {
            $client_secret = $matches[1];
        } else {
            $client_secret = $this->input->post('client_secret');
        }
        
        $this->load->model('code_model');
        $this->load->model('client_model');
        
        // Client_secret must be given either way
        if (!$client_secret || !$grant_type || !$code || !$client_id || !$redirect_uri ) {
            $data['error'] = 'invalid_request';
        
     	// Hard-coded: 'grant-type' must be 'authorization_code'
        } else if ($grant_type != 'authorization_code') {
            $data['error'] = 'unsupported_grant_type'; //'invalid_grant' ;
        

        // Authenticate client
        } else if (!$this->client_model->validate_secret($client_id, $client_secret)) {
            $data['error'] = 'invalid_client';
        
        // Validate code
        } else if (!$this->client_model->validate_redirect_uri($client_id, $redirect_uri)) {
            $data['error'] = '';
        
        // Validate code
        } else if (!$this->code_model->is_valid($code, $client_id)) {
            $data['error'] = 'unauthorized_client';
            
        // Hooray! Give the lad a token!
        }else{
            $this->load->model('access_token_model');
            $result = $this->access_token_model->create( $client_id, $this->session->user );
            $data['access_token'] = $result->access_token ;
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }

}
