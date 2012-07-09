<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_foursquare extends Abstract_service {
    
    private $ci ;
    private $service_name = 'foursquare' ;
    
    function __construct(){
        $this->ci = &get_instance();
        $this->ci->load->library('OAuth2_client', array('service' => $service_name ), $service_name);
    }
    
    function get_authorization_url(){
        return $this->ci->foursquare->authorize();
    }
    
    function user_id( $token ){
        $this->ci->foursquare->api();
    }
}