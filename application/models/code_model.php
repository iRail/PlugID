<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class Code_model extends CI_Model {
    
    private $expires = 600 ; // 10 minutes
    
    /*
     * Create new code
     */
	function create($client_id, $user_id, $redirect_uri) {
	    
        $code = md5(time() . uniqid());
		$data =  array(   'client_id' => $client_id, 
		                  'user_id' => $user_id, 
		                  'code' => $code, 
		                  'expires' => time() + $this->expires,
                          'redirect_uri' => $redirect_uri );
        $this->db->insert('auth_codes', $data);
        return $code ;
	}
    
    /*
     * Check a code (used before giving access token)
     */
    function is_valid( $code, $client_id, $redirect_uri ){
        // check if code is (still) valid
        $where = array( 'code' => $code, 
                        'client_id' => $client_id,
                        'redirect_uri' => $redirect_uri,
                        'expires >' => time() );
        return $this->db->get_where('auth_codes', $where )->num_rows() > 0 ;
    }
	
}