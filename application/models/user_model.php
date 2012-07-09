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
    
    /*
     * Get user who's access_token and service are known
     */
    function get_user_from_service( $service_type, $ext_user_id ){
        $where = array( 'ext_user_id' => $ext_user_id,
                        'service_type' => $service_type
                        );
        return $this->db->get_where('user_tokens', $where)->row();
    }
    
    /*
     * Add or update an access_token of a certain type to a user
     * Leave refresh_token and or external user id null if they did not change
     */
    function set_token( $user_id, $service_type, $access_token, $refresh_token = NULL, $ext_user_id = NULL ){
        $data = new stdClass();
        
        $data->user_id = $user_id;
        $data->service_type = $service_type ;
        $data->access_token = $access_token ;
        if( !is_null($ext_user_id) ){
            $data->ext_user_id = $ext_user_id   ;
        }
        if( !is_null($refresh_token) ){
            $data->refresh_token = $refresh_token ;
        }
        
        $where = array( 'user_id' => $user_id, 'service_type' => $service_type );
        if( $this->db->get_where('user_tokens', $where )->num_rows() == 0 ){
            // insert
            $this->db->insert( 'user_tokens',$data );
        }else{
            // update
            $this->db->update( 'user_tokens',$data, $where );
        }
        
        return $data ;
    }
    
    /*
     * Merge user_id_2 to user_id_1
     */
    function merge( $user_id_1, $user_id_2 ){
        // user2 attributes user1
        $data  = array( 'user_id' => $user_id_1 );
        $where = array( 'user_id' => $user_id_2 );
        
        // these tables should get updated
        $tables = array('user_tokens', 'clients', 'auth_tokens', 'auth_codes' );
        
        // updating tables
        $this->db->update( $tables, $data, $where );
        
        //delete user from users table
        $this->db->delete('users', $where); 
    }
    
    function revoke( $user_id, $client_id ){
        $where = array( 'user_id' => $user_id,
                        'client_id' => $client_id );
        $tables = array('auth_codes','auth_tokens');
        $this->db->delete($tables, $where);
    }
}
