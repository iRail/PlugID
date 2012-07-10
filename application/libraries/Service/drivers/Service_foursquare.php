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

class Service_foursquare extends Abstract_service {
    
    function __construct(){
        parent::__construct('foursquare');
        $this->service_name = 'foursquare';
        $this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
    }
    
    function user_id() {
        $json = $this->ci->{$this->service_name}->api('users/self');
        $result = json_decode($json);
        
        if($result == NULL || $result->meta->code != 200){
        	return FALSE;
        }        
        return $result->response->user->id;
    }
}