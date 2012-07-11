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
        $this->valid_drivers = array('Service_foursquare');//,'Service_facebook','Service_google','Service_viking');
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
    private $hash_algo = 'md5';
    
    /**
     * Constructor
     */
    function __construct( $service_name ){
        $this->ci = &get_instance();
        $this->service_name = $service_name;
    }
    
    /**
     * Generic function to provide a state for securing authorize request
     */
    public function get_state(){
        return $this->ci->session->state = hash($this->hash_algo, time() . uniqid()) ;
    }
    
    /**
     * Get ext_user_id from specific service
     */
    abstract function identify( $callback_data );
    
    /**
     * Can be overwritten if needed
     */
    public function authorize(){
        redirect($this->ci->{$this->service_name}->authorize( array('state'=> $this->get_state()) ));
    }
    
    /**
     * Pass on the token to the library
     */
    abstract function set_identification( $config );
    
    /**
     * proxy calls
     */
    public function api( $endpoint, $params = array(), $method = 'get' ){
        return $this->ci->{$this->service_name}->api($endpoint);
    }
}
