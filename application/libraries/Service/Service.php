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
    
    protected $adapter = NULL;
    protected $valid_drivers = array('Service_foursquare', 'Service_facebook', 'Service_google', 'Service_viking');
    
    /*public function __construct($config = array()) {
        if (isset($config['adapter']) && in_array('service_' . $config['adapter'], array_map('strtolower', $this->valid_drivers))) {
            $this->adapter = $config['adapter'];
        }
    }*/
    
    /**
     * Only executed on first request
     * @param string $child
     */
    function __get($child) {
    	parent::__get($child);
    	
    	$ci = &get_instance();
    	$ci->load->config('services/'.$child, TRUE);
    	$config = $ci->config->item('services/'.$child);
    	$this->$child->config = $config;
    	$this->$child->initialize($config);
    	
    	return $this->$child;
    }
    
    /*function __call($method, $args = array()) {
        return call_user_func_array(array($this->{$this->adapter}, $method), $args);
    }*/
    
    function is_valid($driver) {
        return in_array('Service_'.strtolower($driver), $this->valid_drivers);
    }
}

/*
 * Class to abstract functions from drivers
 */
abstract class Service_driver extends CI_Driver {
	
    protected $ci;
    private $hash_algo = 'md5';
    private $config = array();
    
    /**
     * Constructor
     */
    function __construct() {
        $this->ci = &get_instance();
    }
    
    /**
     * Get ext_user_id and authentication_tokens from specific service
     */
    abstract function callback($data);
    
    /**
     * Gets all params ready and calls build_and_redirect(
     */
    abstract function authorize();
    
    /**
     * Get all necessary tokens to sign requests
     */
    abstract function set_authentication($tokens);
    
    /**
     * proxy calls
     */
    abstract function api($endpoint, $params = array(), $method = 'get');
    
    /**
     * Makes config loading easier
     */
    abstract function initialize($config = array());
    
    /**
     * Generic function to provide a state for securing authorize request
     * Saves the state in Session
     */
    protected function get_state() {
        return $this->ci->session->state = hash($this->hash_algo, time() . uniqid());
    }
}