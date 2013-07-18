<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
class Client_model extends CI_Model {
    
    private $hash_algo = 'md5';
    
    function get($client_id) {
        // obvs.
        $where = array('client_id' => $client_id);
        return $this->db->get_where('clients', $where)->row();
    }
    
    /*
     * 
     */
    function validate_secret( $client_id, $client_secret ){
        $where = array( 'client_id'     => $client_id,
                        'client_secret' => $client_secret);
        return $this->db->get_where('clients', $where)->num_rows() == 1 ;
    }
    
    /*
     * 
     */
    function validate_redirect_uri( $client_id, $redirect_uri ){
        $where = array( 'client_id'     => $client_id,
                        'redirect_uri'  => $redirect_uri );
        return $this->db->get_where('clients', $where)->num_rows() == 1 ;
    }
    
    /*
     * Generate new client_id & client_secret
     */
    function create( $name, $redirect_uri, $user_id ){
        // generate data
        $data = new stdClass();
        $data->client_id     = hash($this->hash_algo, time() . uniqid());
        $data->client_secret = hash($this->hash_algo, time() . uniqid());
        $data->name     = $name ;
        $data->redirect_uri = $redirect_uri;
        $data->user_id  = $user_id;
        
        // check if client_exists
        while( $this->db->get_where('clients', array('client_id' => $data->client_id ) )->num_rows() == 1 ){
            $data->client_id     = md5(time() . uniqid());
        }
        
        // insert row
        $this->db->insert('clients',(array)$data);
        return $data ;
    }
    
    /*
     * Reset client's secret
     */
    function reset_secret($client_id, $type)
    {
        // create new secret
        $data = array(
            $type => hash($this->hash_algo, time() . uniqid()),
        );
        
        // update db
        $this->db->update( 'clients', $data, array('client_id' => $client_id ) );
        
        // return result
        $data->client_id = $client_id;
        return $data ;
    }
    
    /*
     * set some data
     */
    function update( $client_id, $name, $redirect_uri, $notify_uri = NULL){

        if (is_null($name) || is_null($redirect_uri)) return false;
        
        $data = array(
            'name' => $name,
            'redirect_uri' => $redirect_uri,
            'notify_uri' => $notify_uri,
        );
        
        return $this->db->update( 'clients', $data, array('client_id' => $client_id ));
    }
    
    /*
     * remove completely
     */
    function delete( $client_id ){
        $where  = array('client_id' => $client_id);
        $tables = array('clients','auth_codes','auth_tokens');
        $this->db->delete($tables, $where);
        return true ;
    }
}
