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
        $this->valid_drivers = array('Service_foursquare','Service_facebook','Service_google','Service_viking');
        if (isset($config['adapter']) && in_array($config['adapter'], array_map('strtolower', $this->valid_drivers))) {
            $this->adapter = $config['adapter'];
        }
    }
    
    function __call($method, $args = array()) {
        return call_user_func_array(array($this->{$this->adapter}, $method), $args);
    }

}

/*
 * Class to abstract functions from drivers
 */
abstract class Abstract_service extends CI_Driver {
    protected $ci ;
    protected $service_name ;
    
    /**
     * Constructor
     */
    function __construct( $service_name ){
        $this->ci = &get_instance();
    }
    
    /**
     * Function to process code and setup further authentication
     */
    function complete_authorization( $data ){
        $data = $this->ci->{$this->service_name}->get_access_token( $data['code'] );
        $data['ext_user_id'] = $this->user_id();
        return $data ;
    }
    
    /**
     * Get ext_user_id from specific service
     */
    abstract function user_id();
    
    /**
     * Generic function
     */
    function get_authorization_url(){
        return $this->ci->{$this->service_name}->authorize();
    }
    
    /**
     * Pass on the token to the library
     */
    function set_token( $token ){
        $this->ci->{$this->service_name}->set_token($token);
    }
    
    function api(){
        
    }
}
