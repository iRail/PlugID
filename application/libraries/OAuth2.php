<?php
/**
 * Implementing OAuth 2 for clients
 * Using cURL to make the requests
 * @author Lennart Martens <lennart at iRail.be>
 * 
 */
class OAuth2 {
    
    // private members
    private $client_id;
    private $client_secret;
    private $callback_url;
    private $last_response;
    
    public function __construct($client_id, $client_secret, $callback) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->callback_url = $callback;
    }
    
    /**
     * @param url
     * @param code
     * @return array or FALSE if failure. Still has to check on errorcodes.
     */
    public function getAccessToken($url, $code, $use_auth_headers = FALSE) {
    	$params = array(
    			'code' => $code,
    			'client_id' => $this->$client_id,
    			'redirect_uri' => $this->callback_url,
    			'grant_type' => 'authorization_code'
    	);
    	$json;
    	if( $use_auth_headers ){
    		// Not all OAuth2.0 providers accepts Basic Authentication header
    		$auth_header = 'Basic ' . $this->$client_secret ;
    		$json =  $this->curl_post( $url, $params, $auth_header);
    	}else{
    		$params['client_secret'] = $this->$client_secret;
    		$json =  $this->curl_post( $url , $params);
    	}
    	if(empty($json)){
    		return FALSE;
    	}
    	
    	return json_decode($json);    	
    }
    
    /**
     * @return boolean
     */
    public function fetch($url, $params = array(), $method = 'get') {
    	$method = 'curl_' . strtolower($method);
    	
    	if( !is_null($token_type) && preg_match('/bearer/i', $token_type)) {
    		$auth_header = 'Bearer ' . $access_token;
    	} else {
    		$auth_header = 'OAuth ' . $access_token;
    	}
    	$response =  $this->{method}($url, $params, $auth_header);
    	if(empty($response)){
    		return FALSE;
    	}
    	$this->last_response = $response;
    	return TRUE;
    }
    
    /**
     * @return array
     */
    public function refreshAccessToken($url, $refresh_token) {
    	$params = array(
    			'grant_type' => 'refresh_token',
    			'client_id' => $this->$client_id,
    			'refresh_token' => $refresh_token
    	);
    	
    	$auth_header = 'Basic ' . $this->$client_secret ;
    	
    	$json = $this->curl_post($url, $params, $auth_header);
    	$data = json_decode($json);
    	
    	if (isset($data->error)) {
    		$this->error = 'Did not receive refresh token';
    		return FALSE;
    	}
    	return $data ;
    }
    
    /**
     * @return string
     */
    public function getLastResponse() {
    	return $this->last_response;
    }   
    
    private function curl_get($url, $params = array(), $auth_header = NULL) {
    	// build query string
    	if (is_array($params)) {
    		$params = http_build_query($params, NULL, '&');
    	}
    
    	// add parameters to url
    	$url = $url . ( strpos($url, '?') ? '&' : '?' ) . $params;
    
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    	if( !is_null( $auth_header ) ){
    		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header ) );
    	}
    
    	return $this->execute($curl);
    }
    
    private function curl_post($url, $params = array(), $auth_header = NULL) {
    	// build query string
    	if (is_array($params)) {
    		$params = http_build_query($params, NULL, '&');
    	}
    
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_POST, TRUE);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    	if( !is_null( $auth_header ) ){
    		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header ) );
    	}
    
    	return $this->execute($curl);
    }
    
    private function curl_put($url, $params = array(), $auth_header = NULL){
		$curl = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		
		if( !is_null( $auth_header ) ){
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header ) );
		}
		return $this->execute($curl); 
		
    }
    
    private function curl_delete(){
    	$curl = curl_init($url);
    	//CUSTOMREQUEST -> Delete function
    	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,"DELETE");
		if( !is_null( $auth_header ) ){
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header ) );
		}
		return $this->execute($curl); 
    }
    private function curl_execute($curl) {
    	// execute
    	$data = curl_exec($curl);
    
    	// error
    	if ($data === FALSE) {
    		$errno = curl_errno($curl);
    		$error = curl_error($curl);
    
    		curl_close($curl);
    
    		$this->error_code = $errno;
    		$this->error = $error;
    		return FALSE;
    	}
    
    	curl_close($curl);
    	return $data;
    }
}