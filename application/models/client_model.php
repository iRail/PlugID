<?php

class Client_model extends CI_Model {
    
    function get($client_id) {
        // obvs.
        $where = array('client_id' => $client_id);
        return $this->db->get_where('clients', $where)->row();
    }
    
    function create( $name, $callback, $user_id ){
        // generate data
        $data = new stdClass();
        $data->client_id     = md5(time() . uniqid());
        $data->client_secret = md5(time() . uniqid());
        $data->name     = $name ;
        $data->callback = $callback;
        $data->user_id  = $user_id;
        
        // check if client_exists
        while( $this->db->get_where('clients', array('client_id' => $data->client_id ) )->row() !== FALSE ){
            $data->client_id     = md5(time() . uniqid());
        }
        
        // insert row
        $this->db->insert('clients',(array)$data);
        return $data ;
    }
}
