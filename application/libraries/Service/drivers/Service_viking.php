<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Koen De Groote <koen at iRail.be>s
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_viking extends CI_Driver {
    
    private $ci ;
    private $service_name = 'vikingspots';
    
    function __construct(){
        $this->ci = &get_instance();
        $this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
    }
    
    function user_id() {
        $results = $this->ci->vikingspots->api('users',array('user_id' => '123'));
    }
}