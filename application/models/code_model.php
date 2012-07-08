<?php

class Code_model extends CI_Model {
    
    private $expires = 600 ; // 10 minutes
    
	function create($client_id, $user_id, $redirect_uri) {
	    
        $code = md5(time() . uniqid());
		$data =  array(   'client_id' => $client_id, 
		                  'user_id' => $user_id, 
		                  'code' => $code, 
		                  'expires' => time() + $this->expires,
                          'redirect_uri' => $redirect_uri );
        $this->db->insert('auth_codes', $data);
        return $code ;
	}
	
}