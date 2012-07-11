<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
class Access_token_model extends CI_Model {
    
    private $expires = 86400 ; // 1 day
    private $hash_algo = 'sha1' ;
    private $token_version = '$1$';
    
    function create( $client_id, $user_id ){
        
        $data->client_id = $client_id ;
        $data->user_id = $user_id ;
        $data->access_token = $token_version . hash( $this->hash_algo, time() . uniqid()) ;
        $data->expires = $this->expires + time();
        
        $this->db->insert( 'auth_tokens', (array)$data );
        
        return $data ;
    }
    
    function is_valid( $access_token ){
        $where = array( 'access_token' => $access_token,
                        'expires >'    => $time );
        return $this->db->get_where('auth_token',$where)->num_rows() > 0 ;
    }
}