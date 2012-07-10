<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Lennart Martens <lennart at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Service_facebook extends Abstract_service {
    
    private $ci ;
    
    function __construct(){
        parent::__construct('facebook');
        $this->service_name = 'facebook' ;
        $this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
    }
    
    function user_id() {
        $json = $this->ci->{$this->service_name}->api('me');
        
        $result = json_decode($json);
        if($result == NULL || isset($result->error)){
        	return FALSE;
        }
        return $result->id;		
    }
}