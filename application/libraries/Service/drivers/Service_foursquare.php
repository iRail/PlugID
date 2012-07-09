<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_foursquare extends CI_Driver {
    
    private $ci ;
    
    function __construct(){
        $this->ci = &get_instance();
        $this->ci->load->library('OAuth2_client', array('service' => 'foursquare'), 'foursquare');
    }
    
    function user_id() {
        
    }
}