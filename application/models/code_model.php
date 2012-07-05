<?php

class Code_model extends CI_Model {
	
	function insert($client_id, $user_id, $code, $expires) {
		$data =  array('client_id' => $client_id, 'user_id' => $user_id, 'code' => $code, 'expires' => time() + $expires);
        return $this->db->insert('auth_codes', $data);
	}
	
}