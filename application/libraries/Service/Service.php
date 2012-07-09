<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service extends CI_Driver_Library {
    
    protected $adapter = 'foursquare';
    
    public function __construct($config = array()) {
        $this->valid_drivers = array('Service_foursquare','Service_facebook','Service_google');
        if (isset($config['adapter']) && in_array($config['adapter'], array_map('strtolower', $this->valid_drivers))) {
            $this->adapter = $config['adapter'];
        }
    }
    
    function __call($method, $args = array()) {
        return call_user_func_array(array($this->{$this->adapter}, $method), $args);
    }

}

/*
 * 
 */
class Abstract_service extends CI_Driver {
    protected $ci ;
    protected $service_name ;
    
    /*
     * 
     */
    function __construct( $service_name ){
        $this->service_name = $service_name ;
        $this->ci = &get_instance();
        $this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
    }
    
    /*
     * Generic function
     */
    function get_authorization_url(){
        return $this->ci->{$this->service_name}->authorize();
    }
}
