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
        
        // identify client
        $client_id = $this->input->get('client_id');
        $response_type = $this->input->get('response_type');
        $callback = $this->input->get('redirect_uri');
        $state = $this->input->get('state');
        
        // allow button clicked
        if ($this->input->post('allows')) {
            // generate code
            $code = md5(time() . uniqid());
            
            // save code to database
            
            // generate callback url
            $callback = $callback . '?' . http_build_query(array('code' => $code, 'state' => $state));
            
            // redirect back to user website
            redirect($callback);
        } else {
        	// show access screen
            $this->load->view('authenticate', $data);
        }
    }
    
    function access_token() {
    	/* pre-checks:
    	 * - 
    	 * - ...
    	 */
    	
    	$grant_type = $this->input->get('grant_type');
    }

}