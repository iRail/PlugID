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
    
    protected $adapter = NULL ;
    protected $valid_drivers = array('Service_foursquare','Service_facebook','Service_google','Service_viking');
    
    public function __construct($config = array()) {
        if (isset($config['adapter']) && in_array('service_' . $config['adapter'], array_map('strtolower', $this->valid_drivers))) {
            $this->adapter = $config['adapter'];
        }
    }
    
    function __call($method, $args = array()) {
        return call_user_func_array(array($this->{$this->adapter}, $method), $args);
    }
    
    function __isset( $driver_adapter ){
        return $this->adapter == $driver_adapter ;
    }
}

/*
 * Class to abstract functions from drivers
 */
abstract class Abstract_service extends CI_Driver {
    protected $ci ;
    protected $settings ;
    protected $tokens ;
    private $hash_algo = 'md5';
    
    /**
     * Constructor
     */
    function __construct(){
        $this->ci = &get_instance();
    }
    
    /**
     * Get ext_user_id from specific service
     */
    abstract function identify( $callback_data );
    
    /**
     * Gets all params ready and calls build_and_redirect(
     */
    abstract function authorize();
    
    /**
     * Pass on the token to the library
     */
    abstract function set_identification( $tokens );
    
    /**
     * Makes config loading easier
     */
    protected function load_config( $name, $conf_dir = NULL ){
        $file  = is_null( $conf_dir ) ? '' : rtrim($conf_dir,'/') . '/';
        $file .= $name ;
        $this->ci->config->load( $file , TRUE);
        $this->settings = $this->ci->config->item( $file );
    }
    
    /**
     * Simply rounds of this authorization request
     */
    protected function build_and_redirect( $params ){
        redirect( $this->settings['url_authorize'] . '?' . http_build_query($params) );
    }
    
    /**
     * Generic function to provide a state for securing authorize request
     * Saves the state in Session
     */
    protected function get_state(){
        return $this->ci->session->state = hash($this->hash_algo, time() . uniqid()) ;
    }
    
    /**
     * proxy calls
     */
    public function api( $endpoint, $params = array(), $method = 'get' ){
        //return $this->ci->{$this->service_name}->api($endpoint, $params, $method);
    }
}
