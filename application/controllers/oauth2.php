<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class oauth2 extends CI_Controller {
    
    function index() {
        echo 'hello world';
    }
    
    function authorize() {
        /* pre-checks:
    	 * - user logged in yet?
    	 * - token for this user?
    	 * - ...
    	 */
    	// check if signed in
        $user = $this->session->user;
        
        // it's a no go
        if( $user === FALSE ){
            $this->load->view('login');
            return ;
        }
        
        // required
        $client_id = $this->input->get('client_id');
        $response_type = $this->input->get('response_type');
        
        // optional
        $callback = $this->input->get('redirect_uri');
        $state = $this->input->get('state');
        
        // invalid_request
        if (!($client_id && $response_type)) {
            show_error('invalid_request');
        }
        
        // unsupported_response_type
        if ($response_type != 'code') {
            show_error('unsupported_response_type');
        }
        
        $this->load->model('client_model');
        $client = $this->client_model->get($client_id);
        
        // client does not exist
        if (!$client) {
            show_error('unauthorized_client');
        }
        
        // optional callback
        $callback = $callback ? $callback : $client->redirect_uri;
        
        // allow button clicked
        if ($this->input->post('allow')) {
            // generate code
            $code = md5(time() . uniqid());
            
            $this->load->model('code_model');
            $this->code_model->insert($client_id, 'temp', $code, 600);
            
            // generate callback url
            // issue: The endpoint URI MAY include an "application/x-www-form-urlencoded" formatte ([W3C.REC-html401-19991224]) query component ([RFC3986] section 3.4) which MUST be retained when adding additional query parameters.
            $callback = $callback . '?' . http_build_query(array('code' => $code, 'state' => $state));
            
            // redirect back to user website
            redirect($callback);
        
        } else {
            
            // show access screen
            $this->load->view('authenticate', array('client' => $client->name));
        }
    }
    
    function access_token() {
        $this->load->library('OAuth2_server');
        
        // required
        $grant_type = $this->input->post('grant_type');
        $code = $this->input->post('code');
        $callback = $this->input->post('redirect_uri');
        
        // hard-coded: 'grant-type' must be 'authorization_code'
        if( $grant_type != 'authorization_code' ){
            $data = array();
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode( $data ));
    }

}
