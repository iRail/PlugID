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
    protected $valid_drivers = array('Service_foursquare', 'Service_facebook', 'Service_google', 'Service_viking', 'Service_twitter', 'Service_instagram');
    
    /**
     * Only executed on first request
     * @param string $child
     * @return object $child
     */
    function __get($child) {
        parent::__get($child);
        
        $ci = &get_instance();
        $ci->load->config('services/' . $child, TRUE);
        $config = $ci->config->item('services/' . $child);
        
        $this->$child->config = $config;
        $this->$child->initialize($config);
        
        return $this->$child;
    }
    
    /**
     * Try and see if a driver is available before loading and using it
     * @param string $driver drivername
     */
    function is_valid($driver) {
        return in_array('Service_' . strtolower($driver), $this->valid_drivers);
    }
}

/*
 * Class to abstract functions from drivers
 */
abstract class Service_driver extends CI_Driver {
    
    protected $ci;
    private $hash_algo = 'md5';
    
    public $config = array();
    
    /**
     * Constructor
     */
    function __construct() {
        $this->ci = &get_instance();
    }
    
    /**
     * CodeIgniter proxy
     */
    function __get($attr) {
        return $this->ci->$attr;
    }
    
    /**
     * Get access_token & ext_user_id
     * 
     * @param oject $callback_data contains
     * @return  FALSE on failure
     * object->ext_user_id
     * object->access_token
     * object->refresh_token (if given)
     * object->expires (if given)
     * object->token_type (if given)
     */
    abstract function callback($data);
    
    /**
     * Gets all params ready and calls build_and_redirect(
     */
    abstract function authorize();
    
    /**
     * This function is used to give the tokens to the driver. With this, the driver can sign its request
     * 
     * @param object $tokens(->access_token)
     */
    abstract function set_authentication($tokens);
    
    /**
     * Make an api call to the service and sign it with the tokens given in set_authentication
     * 
     * @param string $endpoint_uri
     * @param array $params for passing all post/get parameters
     * @param enum(get,post) $method
     * @return string: returns all content of the http body returned on the request
     */
    abstract function api($endpoint, $params = array(), $method = 'get');
    
    /**
     * give config array with needed parameters like client_id, $client_secret etc.
     * @param array $config (loaded in service & passed)
     */
    abstract function initialize($config = array());
    
    /**
     * Generic function to provide a state for securing authorize request
     * @return string $state, as it is saved in the session
     */
    protected function get_state() {
        $state = hash($this->hash_algo, time() . uniqid()) ;
        $this->ci->session->set_flashdata('state', $state );
        return $state;
    }
}
