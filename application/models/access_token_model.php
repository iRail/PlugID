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
        
        // generate data
        $data->client_id = $client_id ;
        $data->user_id = $user_id ;
        $data->access_token = $this->token_version . hash( $this->hash_algo, time() . uniqid()) ;
        $data->expires = $this->expires + time();
        
        // check for existance
        while( $this->db->get_where('auth_tokens', array('access_token' => $data->access_token ) )->num_rows() == 1 ){
            $data->access_token = $this->token_version . hash( $this->hash_algo, time() . uniqid()) ;
        }
        
        $this->db->delete( array( 'user_id' => $user_id, 'client_id' => $client_id ));
        $this->db->insert( 'auth_tokens', (array)$data );
        
        return $data ;
    }
    
    function is_valid( $access_token ){
        $where = array( 'access_token' => $access_token,
                        'expires >'    => time() );
        return $this->db->get_where('auth_tokens',$where)->row() ;
    }
}
