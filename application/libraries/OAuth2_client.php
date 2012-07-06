<?php

class OAuth2_client extends client {
    
	/**
	 * Necessary vars
	 * Store it in configfile?
	 */
    public $url_authorize = '';
    public $url_access_token = '';
    public $url_api_base = '';
    public $callback;
    public $client_id = '';
    public $client_secret = '';
    
    private $settings, $ci, $service, $token = FALSE;
    
    function __construct($service, $scope = NULL) {
    	$this->ci = &get_instance();
    
    	// get config
    	$this->ci->config->load(strtolower($service), TRUE);
    	$this->service = $service;
    	$this->settings = $this->ci->config->item(strtolower($service));
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
     * Redirect to authorize url
     * @param array $options
     */
    public function authorize($options = array()) {
        // build params
        $params = array();
        $params['client_id'] = $this->settings['client_id'];
        $params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->settings['callback_url'];
        $params['state'] = md5(uniqid(rand(), TRUE));
        $params['response_type'] = 'code';
        
        if ($options['scope']) {
            $params['scope'] = $options['scope'];
        }
        
        $url = $this->settings['url_authorize'];
        return $url . (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
    }
    
    /**
     * Request access token from code
     * @param string $code
     * @param array $options
     */
    public function get_access_token($code, $options = array()) {
        $params = array();
        $params['code'] = $code;
        $params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->settings['callback_url'];
        $params['grant_type'] = 'authorization_code';
        $params['client_id'] = $this->settings['client_id'];
        $params['client_secret'] = $this->settings['client_secret'];
        
        $ci = &get_instance();
        $ci->load->library('curl');
        
        //Post as stated in http://tools.ietf.org/html/draft-ietf-oauth-v2-28#section-4.4.2
        $data = $ci->curl->post($this->settings["url_access_token"], $params);
        $json = json_decode($data);
        
        if (isset($json->error) || ! isset($json->access_token)) {
        	return FALSE;
        }        
        // response
        $token = $json->access_token;
        $token_type = $json->token_type ? $json->token_type : FALSE;
        $refresh_token = $json->refresh_token ? $json->refresh_token : FALSE;
        $scope = $json->scope ? $json->scope : FALSE;
        
        return array($token, $refresh_token, $token_type, $scope);
    }
    
    /**
     * Make API calls
     * @param string $path
     * @param string $method
     * @param array $params
     */
    public function api($uri, $method = 'GET', $data = array()) {
		$params = array();
        $ci = &get_instance();
        $ci->load->library('curl');
        $params['oauth_token'] = $token;
                     
        if (strtoupper($method) == 'POST') {
        	$json = $ci->curl->post($this->settings['url_api_base'] . $uri . '?' . http_build_query($params), $data);
        } 
        else {
        	$json = $ci->curl->get($this->settings['url_api_base'] . $uri . '?' . http_build_query(array_merge($params, $data)));
        }
        $data = json_decode($json);
        if (!$json) {
        	$this->error = 'No response from ' . $service . ' API';
        	return FALSE;
        } 
        //What to do when an error was returned?
        // 1) Try to refresh the token
        // 2) If it fails again, return FALSE - token isn't usable anymore
        // Not finished yet
        elseif (isset($data->error)){
		    //Try to get a new token
			$this->refresh_access_token($refresh_token);
			
			//Readd new token
            if (strtoupper($method) == 'POST') {
        	    $json = $ci->curl->post($this->settings['url_api_base'] . $uri . '?' . http_build_query($params), $data);
            } 
            else {
        	    $json = $ci->curl->get($this->settings['url_api_base'] . $uri . '?' . http_build_query(array_merge($params, $data)));
            }			
			return json_decode($data);
		}
		return $json;
    }
    
    public function refresh_access_token($refresh_token)
    {
    	$params = array(
    			'grant_type' => 'refresh_token',
    			'client_id' => $this->settings['client_id'],
    			'client_secret' => $this->settings['client_secret'],
    			'refresh_token' => $refresh_token,
    	);
    	$ci = &get_instance();
    	$ci->load->library('curl');
    	
    	$data = $ci->curl->post($url_access_token, $params);
    	$json =  json_decode($data);
    	
    	if(isset($json->error))
    	{
    		return FALSE;
    	}
    	//To Do : save new token and refresh_token in DB and set it in this class.
    }
    
    

}
