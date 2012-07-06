<?php

abstract class OAuth2_client extends client {
    
	function get_access_token();
    
    function create_authorize_url();
    
    function api();

    function set_token( $toke );
    
    function api( $uri, $data = array(), $method = 'GET' );

    function get_userid();
    
    protected function _get(){
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_HTTPGET, true );
        curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE); 
        
        return curl_exec($c);
    }
}
