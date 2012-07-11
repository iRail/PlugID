<?php
/**
 * OAuth 1.0a client, based on the OAuth class provided by PHP.net
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lennart Martens <Lennart at iRail.be>
 * 
 * Sample:
 * 1) Obtaining request_token: oauth1->get_request_token()
 * 2) Set oauth_token and oauth_token_secret: oauth1->set_authentication()
 * 3) Perform a redirect: get the URL from oauth1->authorize and obtain the oauth_verifier 
 * 4) Get the access_token with the oauth_verifier: oauth1->get_access_token($oauth_verifier)
 * 5) perform a request to a protected resource: oauth1->api(endpoint, params,method): returns a JSON
 */

class OAuth1_client {

	private $settings, $ci, $service, $oauth;
	private $oauth_token = FALSE;

	function __construct( $config = array() ){
		
		$this->ci = &get_instance();
		$this->service = $config['service'];
		$this->ci->load->library('Session');
		
		// get config
		$this->ci->config->load('oauth1/' . $this->service, TRUE);
		$this->settings = $this->ci->config->item('oauth1/' . $this->service );
		
		$oauth = new OAuth($this->settings['consumer_key'],$this->settings['consumer_secret']);
	}
	
	public function set_authentication( $config ){
		$oauth_token = $config->oauth_token;
		$oauth->setToken($config->oauth_token, $config->oauth_token_secret);
	}
	
	/** Gets the request token
	 * 
	 * @return array([oauth_token] => some_token, [oauth_token_secret] => some_token_secret)
	 * Twitter: extra in array: oauth_callback_confirmed : must be TRUE
	 */
	public function get_request_token()
	{
		//Build URL
		$url = $this->settings['url_request_token'];
		return $oauth->getRequestToken($url, $this->settings['callback_url']);
	}
	
	
	public function get_access_token($oauth_verifier)
	{
		//Build URL
		$url = $this->settings['url_access_token'];
		return $oauth->getAccessToken($url, '' , $oauth_verifier);
	}
	
	/** Authorize the application
	 * Build URL to redirect to
	 * 
	 */
	public function authorize()
	{
		$url = $this->settings['url_authorize'];
		return $url . (strpos($url, '?') !== false ? '&' : '?') . 'oauth_token=' . $this->oauth_token;
	}
	
    /**
     * Make API calls
     * @param string $endpoint
     * @param associative array $params
     * @return plain json
     */
    function api( $endpoint, $params = array(), $method = 'get' ){
        
        $url = rtrim($this->settings['url_api_base'], '/') . '/' . trim( $endpoint , '/') ;
		$method = strtoupper($method);
		$oauth->fetch($url,$params, $method);
        return $oauth->getLastResponse();
    }
	
			
}