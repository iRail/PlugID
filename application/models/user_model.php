<?php

class User_model extends CI_Model {
    
    /*
     * obvs.
     */
    function get($user_id) {
        // obvs.
        $where = array('user_id' => $user_id);
        return $this->db->get_where('users', $where)->row();
    }
    
    /*
     * Create new user and return user_id
     */
    function create(){
        // generate data
        $data = new stdClass();
        $data->user_id = rand(1000000000, getrandmax());
        
        // check if client_exists
        while( $this->db->get_where('users', array('user_id' => $data->user_id ) )->num_rows() == 1 ){
            $data->user_id = rand(1000000000, getrandmax());
        }
        
        // insert row
        $this->db->insert('users',(array)$data);
        return $data ;
    }
    
    /**
     * 
     */
    function get_tokens( $user_id, $service_type ){
        $where = array( 'user_id' => $user_id,
                        'service_type' => $service_type
                      );
        return $this->db->get_where('user_tokens', $where)->row();
    }
    
    /**
     * Get user who's access_token and service are known
     */
    function get_token_by_ext_id( $service_type, $ext_user_id ){
        $where = array( 'ext_user_id' => $ext_user_id,
                        'service_type' => $service_type
                        );
        return $this->db->get_where('user_tokens', $where)->row();
    }
    
    /*
     * Add or update an access_token of a certain type to a user
     * Leave refresh_token and or external user id null if they did not change
     */
    function set_token( $data ){
        
        $where = array( 'user_id' => $data['user_id'], 'service_type' => $data['service_type'] );
        if( $this->db->get_where('user_tokens', $where )->num_rows() == 0 ){
            // insert
            $this->db->insert( 'user_tokens',$data );
        }else{
            // update
            $this->db->update( 'user_tokens',$data, $where );
        }
        return $data ;
    }
    
    /**
     * 
     */
    function authorize_client( $user_id, $client_id ){
        if( !$this->is_client_authorized($user_id, $client_id) ){
            $data = array( 'user_id' => $user_id,
                           'client_id' => $client_id );
            return $this->db->insert('auth_clients', $data);
        }
        return TRUE;    
    }
    
    /**
     * 
     */
    function is_client_authorized( $user_id, $client_id ){
        $where = array( 'user_id' => $user_id,
                        'client_id' => $client_id );
        return $this->db->get_where('auth_clients', $where)->num_rows() != 0 ;
    }
    
    /*
     * Get all user's clients
     */
    function get_clients( $user_id ){
        $where = array('user_id' => $user_id);
        return $this->db->get_where('clients', $where)->result() ;
    }
    
    /*
     * Merge user_id_2 to user_id_1
     */
    function merge( $user_id_1, $user_id_2 ){
        // user2 attributes user1
        $data  = array( 'user_id' => $user_id_1 );
        $where = array( 'user_id' => $user_id_2 );
        
        // updating tables
        $this->db->update( 'user_tokens', $data, $where );
        $this->db->update( 'clients',     $data, $where );
        $this->db->update( 'auth_tokens', $data, $where );
        $this->db->update( 'auth_codes',  $data, $where );
        
        //delete user from users table
        $this->db->delete('users', $where); 
    }
    
    /**
     * Delete user-client combo from all authentication tables
     */
    function revoke( $user_id, $client_id ){
        $where = array( 'user_id' => $user_id,
                        'client_id' => $client_id );
        $tables = array('auth_codes','auth_tokens','auth_clients');
        $this->db->delete($tables, $where);
    }
}
