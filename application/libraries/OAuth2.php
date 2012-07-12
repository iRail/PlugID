<?php

class OAuth2 {
    
    // private members
    private $client_id;
    private $client_secret;
    
    public function __construct($client_id, $client_secret, $callback) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }
    
    public function getAccessToken($url, $code) {
    	
    }
    
    public function fetch($url, $params = array(), $method = 'get') {
    	$method = strtolower($method);
    }
    
    public refreshAccessToken($url, $token) {
    	
    }

}