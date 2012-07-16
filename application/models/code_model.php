<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class Code_model extends CI_Model {
    
    private $expires = 600 ; // 10 minutes
    private $hash_algo = 'sha1' ;
    
    /*
     * Create new code
     */
	function create($client_id, $user_id) {
        $code = hash($this->hash_algo, time() . uniqid());
        // delete possible existing row
	    $where = array('client_id' => $client_id, 'user_id' => $user_id);
        if( $this->db->get_where('auth_codes', $where )->num_rows() == 1 ){
            $this->db->delete('auth_codes',$where);
        }
		$data =  array(   'client_id' => $client_id, 
		                  'user_id' => $user_id, 
		                  'code' => $code, 
		                  'expires' => time() + $this->expires );
        $this->db->insert('auth_codes', $data);
        return $code ;
	}
    
    /*
     * Check a code (used before giving access token)
     */
    function is_valid( $code, $client_id ){
        // check if code is (still) valid
        $where = array( 'code' => $code, 
                        'client_id' => $client_id,
                        'expires >' => time() );
        return $this->db->get_where('auth_codes', $where )->row() ;
    }
	
}