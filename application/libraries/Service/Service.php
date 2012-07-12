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
    
    public function __construct($config = array()) {
        if (isset($config['adapter']) && in_array('service_' . $config['adapter'], array_map('strtolower', $this->valid_drivers))) {
            $this->adapter = $config['adapter'];
        }
    }
    
    function __call($method, $args = array()) {
        return call_user_func_array(array($this->{$this->adapter}, $method), $args);
    }
    
    function is_valid($driver) {
        return in_array('Service_'.strtolower($driver), $this->valid_drivers);
    }
}

/*
 * Class to abstract functions from drivers
 */
abstract class Abstract_service extends CI_Driver {
    protected $ci;
    protected $settings;
    private $hash_algo = 'md5';
    
    /**
     * Constructor
     */
    function __construct() {
        $this->ci = &get_instance();
    }
    
    /**
     * Get ext_user_id and authentication_tokens from specific service
     */
    abstract function callback($callback_data);
    
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
    abstract function api( $endpoint, $params = array(), $method = 'get' );
    
    /**
     * Makes config loading easier
     */
    protected function load_config($name, $conf_dir = NULL) {
        $file = is_null($conf_dir) ? '' : rtrim($conf_dir, '/') . '/';
        $file .= $name;
        $this->ci->config->load($file, TRUE);
        $this->settings = $this->ci->config->item($file);
    }
    
    /**
     * Simply rounds of this authorization request
     */
    protected function build_and_redirect($params) {
        redirect($this->settings['url_authorize'] . '?' . http_build_query($params));
    }
    
    /**
     * Generic function to provide a state for securing authorize request
     * Saves the state in Session
     */
    protected function get_state() {
        return $this->ci->session->state = hash($this->hash_algo, time() . uniqid());
    }
}

abstract class Abstract_oauth2_service extends Abstract_service {
    
	private $oauth2;
	
    function __construct() {
        parent::__construct();        
    }
    function initialize($config = array())    {
    	$this->oauth2 = new OAuth2($config['client_id'], $config['client_secret'], $config['callback']);
    }
    
    
    //abstract function callback( $callback_data );
    //abstract function authorize();
    //abstract function set_authentication( $tokens );
    

    protected function setup_oauth_client_lib() {
        $this->ci->oauth2->callback_url = $this->settings['callback_url'];
        $this->ci->oauth2->url_authorize = $this->settings['url_authorize'];
        $this->ci->oauth2->url_access_token = $this->settings['url_access_token'];
        $this->ci->oauth2->url_api_base = $this->settings['url_api_base'];
    }
}
