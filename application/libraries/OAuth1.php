<?php
/**
 * Implementing OAuth 1 for clients
 * Using tmhOAuth
 * @author Jens Segers <jens at iRail.be>
 * 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require (APPPATH . '/libraries/oauth1/tmhOAuth.php');

class OAuth1 {
    
    private $tmhOAuth;
    
    function __construct($consumer_key = FALSE, $consumer_secret = FALSE) {
        $config = array();
        $config['consumer_key'] = $consumer_key;
        $config['consumer_secret'] = $consumer_secret;
        $config['curl_ssl_verifyhost'] = 0;
        $config['curl_ssl_verifypeer'] = FALSE;
        
        $this->tmhOAuth = new tmhOAuth($config);
    }
    
    /**
     * Magic call method that passes every call to the R object
     * @return mixed
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->tmhOAuth, $name), $arguments);
    }
    
    /**
     * Magic get method that passes every property request to the R object
     * @return mixed
     */
    public function __get($name) {
        return $this->tmhOAuth->$name;
    }
    
    /**
     * @return boolean
     */
    public function fetch($url, $params = array(), $method = 'get') {
        if (!isset($params['oauth_token']) || !isset($params['oauth_token_secret'])) {
            return FALSE;
        }
        
        $this->tmhOAuth->config['user_token'] = $params['oauth_token'];
        $this->tmhOAuth->config['user_secret'] = $params['oauth_token_secret'];
        unset($params['oauth_tokens']);
        unset($params['oauth_token_secret']);
        
        $code = $this->tmhOAuth->request(strtoupper($method), $url, $params);
        
        if ($code == 200) {
            return $this->last_response = $this->tmhOAuth->response['response'];
        } else {
            return FALSE;
        }
    }
    
    /**
     * @return string
     */
    public function getLastResponse() {
        return $this->last_response;
    }
    
    /**
     * @param string $url
     * @param string $code
     * @param boolean $use_auth_headers
     * @return array or FALSE if failure. Still has to check on errorcodes.
     */
    public function getAccessToken($url, $params = array()) {
        if (!isset($params['oauth_verifier'])) {
            return FALSE;
        }
        
        $this->tmhOAuth->config['user_token'] = $params['oauth_token'];
        $this->tmhOAuth->config['user_secret'] = $params['oauth_token_secret'];
        unset($params['oauth_tokens']);
        unset($params['oauth_token_secret']);
        
        $code = $this->tmhOAuth->request('POST', $url, $params);
        
        if ($code == 200) {
            return $this->tmhOAuth->extract_params($this->tmhOAuth->response['response']);
        } else {
            return FALSE;
        }
    }
    
    /**
     * @param string $url
     * @param string $code
     * @param boolean $use_auth_headers
     * @return array or FALSE if failure. Still has to check on errorcodes.
     */
    public function getRequestToken($url, $params = array()) {
        if (!isset($params['oauth_callback'])) {
            return FALSE;
        }
        
        $code = $this->tmhOAuth->request('POST', $url, $params);
        
        if ($code == 200) {
            return $this->tmhOAuth->extract_params($this->tmhOAuth->response['response']);
        } else {
            return FALSE;
        }
    }

}