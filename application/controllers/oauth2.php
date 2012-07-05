<?php

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
        
        // required
        $client_id = $this->input->get('client_id');
        $client_secret = $this->input->get('client_secret');
        $response_type = $this->input->get('response_type');
        
        // optional
        $callback = $this->input->get('redirect_uri');
        $state = $this->input->get('state');
        
        // prepare
        $data = new stdClass();
        
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
            $callback = $callback . '?' . http_build_query(array('code' => $code, 'state' => $state));
            
            // redirect back to user website
            redirect($callback);
        
        } else {
            
            // show access screen
            $data->client = $client->name;
            $this->load->view('authenticate', $data);
        }
    }
    
    function access_token() {
        /* pre-checks:
    	 * - 
    	 * - ...
    	 */
        
        $grant_type = $this->input->get('grant_type');
        $code = $this->input->get('code');
    }

}
