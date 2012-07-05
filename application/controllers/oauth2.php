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
        $this->load->database();
        
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
        if( !( $client_id && $client_secret && $response_type ) ){
            $data->error = "1" ;
            $data->error_msg = 'invalid_request';
            $this->load->view('authenticate', $data);
            return ;
        }
        
        // unsupported_response_type
        if( $response_type != 'code' ){
            $data->error = "4" ;
            $data->error_msg = "unsupported_response_type" ;
            $this->load->view('authenticate', $data);
            return ;
        }
        
        // unauthorized_client
        $where = array('client_id' => $client_id, 'client_secret' => $client_secret );
        $query = $this->db->get_where('clients', $where );
        
        if( $query->num_rows() != 1 ){
            $data->error = "2" ;
            $data->error_msg = "unauthorized_client" ;
            $this->load->view('authenticate', $data);
            return ;
        }
        $row = $query->row();
        $name = $row->name ;
        // optional callback
        $callback = $callback? $callback : $row->redirect_uri ;
        
        // allow button clicked
        if ($this->input->post('allows')) {
            // generate code
            $code = md5(time() . uniqid());
            
            // save code to database
            $row = array(
                'client_id' => $client_id,
                'user_id' => 'temp', // CHANGE TH!S
                'code' => $code,
                'expires' => time() + 60 * 10 // ten minutes
            );
            $this->db->insert('auth_codes', $row);
            
            // generate callback url
            $callback = $callback . '?' . http_build_query(array('code' => $code, 'state' => $state));
            
            // redirect back to user website
            redirect($callback);
        } else {
        	// show access screen
            $data->client = $name ;
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
