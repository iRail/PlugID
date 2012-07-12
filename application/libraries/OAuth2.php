<?php

class OAuth2 {
    
    // private members
    private $client_id;
    private $client_secret;
    private $callback_url;
    
    public function __construct($client_id, $client_secret, $callback) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->callback_url = $callback;
    }
    
    /**
     * @return array
     */
    public function getAccessToken($url, $code) {
    	
    }
    
    /**
     * @return boolean
     */
    public function fetch($url, $params = array(), $method = 'get') {
    	$method = strtolower($method);
    }
    
    /**
     * @return array
     */
    public function refreshAccessToken($url, $token) {
    	
    }
    
    /**
     * @return string
     */
    public function getLastResponse() {
    	
    }

}