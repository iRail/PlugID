<?php

class User_model extends CI_Model {
    
    /**
     * @return obj->user_id
     */
    function get($user_id) {
        // obvs.
        $where = array('user_id' => $user_id);
        return $this->db->get_where('users', $where)->row();
    }
    
    /**
     * Create new user and return user_id
     * @return object->user_id
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
     * @param int $user_id
     * @param string $service_type
     * @return array
     */
    function get_tokens( $user_id, $service_type = FALSE ){
        $this->db->where('user_id', $user_id);
        if( $service_type !== FALSE ){
            $this->db->where('service_type',$service_type);
        }
        return $this->db->get('user_tokens')->result();
    }
    
    /**
     * Get user who's access_token and service are known
     * ONLY TO BE USED TO SEE IF EXT_USER_ID IS CONNECTED TO A INTERNAL USER_ID (in the past) 
     * @param string $service_type
     * @param string $external user id
     * @return empty array
     *      or int    object->user_id
     *         string object->service_type (this and next params may be NULL)
     *         string object->access_token
     *         string object->refresh_token
     *         string object->oauth_token
     *         string object->oauth_secret
     *         int    object->expires (actual timestamp)
     */
    function get_token_by_ext_id( $service_type, $ext_user_id ){
        $where = array( 'ext_user_id' => $ext_user_id,
                        'service_type' => $service_type,
                        );
        return $this->db->get_where('user_tokens', $where)->row();
    }
    
    /**
     * Add or update an access_token of a certain type to a user
     * Leave refresh_token and or external user id null if they did not change
     * @param array data(user_id, ext_user_id, service_type,  // ← required
     *                   access_token, refresh_token, expires, oauth_token, oauth_token_secret ) // ← optional
     * @return same array
     */
    function set_token( $data ){
        if( $this->db->get_where('users',array('user_id' => $data['user_id']))->num_rows() == 0 ){
            return FALSE ;
        }
        $where = array( 'user_id' => $data['user_id'], 'service_type' => $data['service_type'] );
        if( $this->db->get_where('user_tokens', $where )->num_rows() == 0 ){
            // insert
            return $this->db->insert( 'user_tokens',$data );
        }else{
            // update
            return $this->db->update( 'user_tokens',$data, $where );
        }
    }
    
    /**
     * @param int user_id
     * @param string client_id
     * @return TRUE of FALSE, it may be, she's still out to get meeee ♬ (boolean)
     */
    function authorize_client( $user_id, $client_id ){
        if( $this->db->get_where('users',array('user_id' => $data['user_id']))->num_rows() == 0 ){
            return FALSE ;
        }
        if( !$this->is_client_authorized($user_id, $client_id) ){
            $data = array( 'user_id' => $user_id,
                           'client_id' => $client_id );
            return $this->db->insert('auth_clients', $data);
        }
        return TRUE;
    }
    
    /**
     * @param int user_id
     * @return array of clients
     */
    function authorized_clients( $user_id ){
         $query = 'select * from auth_clients a join clients c on a.client_id = c.client_id where a.user_id = ? ';
         return $this->db->query( $query, array($user_id) )->result();
    }
    
    /**
     * @param int $user_id
     * @param string $user_id
     */
    function is_client_authorized( $user_id, $client_id ){
        $where = array( 'user_id' => $user_id,
                        'client_id' => $client_id );
        return $this->db->get_where('auth_clients', $where)->num_rows() != 0 ;
    }
    
    /*
     * Get all user's clients
     * @param int $user_id
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
