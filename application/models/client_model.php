<?php

class Client_model extends CI_Model {
    
    function get($client_id) {
        $where = array('client_id' => $client_id);
        return $this->db->get_where('clients', $where)->row();
    }

}