<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Foursquare_API {
    
    private $settings, $ci, $token = FALSE;
    
    // contains the last error
    public $error = FALSE;
    
    function __construct() {
        $this->ci = &get_instance();
        
        // get config
        $this->ci->config->load('foursquare', TRUE);
        $this->settings = $this->ci->config->item('foursquare');
    }
    
    /**
     * Set the token to use for following request
     */
    function token() {
        return $this->token;
    }
    
    /**
     * Get the current token
     * @param string $token
     */
    function set_token($token) {
        return $this->token = $token;
    }
    
    /**
     * Authorization url
     * @param string $callback
     * @return string
     */
    function auth_url($callback = FALSE) {
        if (!$callback) {
            $callback = $this->settings['callback_url'];
        }
        
        return 'https://foursquare.com/oauth2/authenticate?client_id=' . $this->settings['client_id'] . '&response_type=code&redirect_uri=' . urlencode($callback);
    }
    
    /**
     * Get OAuth token
     * @param string $code
     * @return string
     */
    function request_token($code) {
        $url = 'https://foursquare.com/oauth2/access_token?client_id=' . $this->settings['client_id'] . '&client_secret=' . $this->settings['client_secret'] . '&grant_type=authorization_code&redirect_uri=' . urlencode($this->settings['callback_url']) . '&code=' . $code;
        $json = $this->_get($url);
        
        if (!isset($json->access_token)) {
            $this->error = 'Did not receive authentication token';
            return FALSE;
        }
        
        $this->set_token($json->access_token);
        return $json->access_token;
    }
    
    /**
     * Foursquare API request method
     * @param string $uri
     * @param array $data
     * @return Object
     */
    function api($uri, $data = array(), $method = 'GET') {
        // url parameters
        $params = array();
        
        // active token set?
        if (!$token = $this->token()) {
            // asume userless access (https://developer.foursquare.com/overview/auth#userless)
            $params['client_id'] = $this->settings['client_id'];
            $params['client_secret'] = $this->settings['client_secret'];
        } else {
            $params['oauth_token'] = $token;
        }
        
        if (strtoupper($method) == 'POST') {
            $json = $this->_post('https://api.foursquare.com/v2/' . $uri . '?' . http_build_query($params), $data);
        } else {
            $json = $this->_get('https://api.foursquare.com/v2/' . $uri . '?' . http_build_query(array_merge($params, $data)));
        }
        
        if (!$json) {
            $this->error = 'No response from Foursquare API';
            return FALSE;
        } elseif ($json->meta->code != 200) {
            $this->error = $json->meta->errorDetail;
            return FALSE;
        }
        
        return $json;
    }
    
    /**
     * Raw CURL get method
     * @param string $url
     * @return Object
     */
    private function _get($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $data = curl_exec($curl);
        curl_close($curl);
        
        if (!$data) {
            return FALSE;
        }
        
        return json_decode($data);
    }
    
    /**
     * Raw CURL post method
     * @param string $url
     * @param array $data 
     * @return Object
     */
    private function _post($url, $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $data = curl_exec($curl);
        curl_close($curl);
        
        if (!$data) {
            return FALSE;
        }
        
        return json_decode($data);
    }

}
