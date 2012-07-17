<?php
/**
 * Implementing OAuth 1 for clients
 * Wrapping the library tmhOAuth
 * @author Jens Segers <jens at iRail.be>
 * @author Lennart Martens <lennart at iRail.be>
 * 
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require (APPPATH . '/libraries/oauth1/tmhOAuth.php');

class OAuth1 {
    
    private $tmhOAuth;///< Holds an object of the class tmhOAuth, a OAuth 1.0a library
    
    /** 
     * Constructs a new OAuth1 object. 
     * All parameters have to be set on false, because it will be autoloaded.
     * 
     * @param  string $consumer_key : consumer_key, get it from dev.twitter.com/apps
     * @param  string $consumer_secret : consumer_secret, get it from dev.twitter.com/apps
     */
    function __construct($consumer_key = FALSE, $consumer_secret = FALSE) {
        $config = array();
        $config['consumer_key'] = $consumer_key;
        $config['consumer_secret'] = $consumer_secret;
        
        //Disables a part of the SSL security => has to be implemented in final version
        $config['curl_ssl_verifyhost'] = 0;
        $config['curl_ssl_verifypeer'] = FALSE;
        
        $this->tmhOAuth = new tmhOAuth($config);
    }
    
    /**
     * Magic call method that passes every call to the tmhOAuth object
     * @return mixed
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->tmhOAuth, $name), $arguments);
    }
    
    /**
     * Magic get method that passes every property request to the tmhOAuth object
     * @return mixed
     */
    public function __get($name) {
        return $this->tmhOAuth->$name;
    }
    
    /**
     * Gets the result of a call to an endpoint of the API
     * 
     * @param string $url : the full URL to the requested endpoint
     * @param $params : extra parameters (such as POST-parameters), optional
     * @param string $method : default 'get'.
     * @return string $response if success, else return FALSE
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
     * Get the latest response of the API
     * @return string
     */
    public function getLastResponse() {
        return $this->last_response;
    }
    
    /**
     * Retrieves an access token of the API.
     * 
     * @param string $url : complete url to the access_token endpoint
     * @param string $params : must contain oauth_token and oauth_token_secret (both from getRequestToken) and oauth_verifier (from the callback)
     * @return array or FALSE if failure.
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
     * Get a request token of the API
     * First step in authentication
     * 
     * @param string $url : full URL to the request_token endpoint
     * @param boolean $params : must contain oauth_callback.
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