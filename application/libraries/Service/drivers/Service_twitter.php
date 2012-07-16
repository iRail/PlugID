<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Lennart Martens <lennart at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_twitter extends Service_driver {
    
    private $oauth, $oauth_token, $oauth_token_secret;
    private $url_authorize = 'oauth/authorize';
    private $url_request_token = 'oauth/request_token';
    private $url_access_token = 'oauth/access_token';
    private $url_base = 'api.twitter.com';
    
    /**
     * give config array with needed parameters like client_id, $client_secret etc.
     * @param array $config (loaded in service & passed)
     */
    function initialize($config = array()) {
		$this->oauth = new tmhOAuth(array('consumer_key'    => $config['consumer_key'],
        									'consumer_secret' => $config['consumer_secret'],
        									'host' => $this->url_base,
        									));
    }
    
    /**
     * Redirect user to start authentication proces to authorize application to remote oauth provider
     */
    function authorize() {
    	$code = $this->oauth->request('POST',$this->oauth->url($this->url_request_token, ''),array('oauth_callback' => $this->config['redirect_uri']));
    	if ($code == 200) {
    		$_SESSION['oauth'] = $this->oauth->extract_params($this->oauth->response['response']);
    		$authurl = $this->oauth->url($this->url_authorize, '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
  			redirect($authurl);
    	} else {
    		echo "mislukt";
    		return FALSE;
    	}
    }
    
    /**
     * Get access_token & ext_user_id
     * 
     * @param oject $callback_data contains ->code to finish authentication
     * @return  FALSE on failure
     *          object->ext_user_id
     *          object->access_token
     *          object->refresh_token (if given)
     */
    function callback($data) {
    	$this->oauth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
    	$this->oauth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
    	
    	$code = $this->oauth->request('POST', $this->oauth->url($this->url_access_token, ''), array('oauth_verifier' => $_REQUEST['oauth_verifier']));
		if ($code == 200) {
			$access_token = array();
			$access_token = $this->oauth->extract_params($this->oauth->response['response']);
			unset($_SESSION['oauth']);
			// To DO: Get User ID
			
			
        	$auth = new stdClass();
        	$auth->ext_user_id = (int) $user->response->user->id;
        	$auth->oauth_token = $access_token['oauth_token'];
        	$auth->oauth_token_secret = $access_token['oauth_token_secret'];
        	
        	return $auth;
		} else {
			return FALSE;
		}
    }
    
    /**
     * This function is used to give the tokens to the driver. With this, the driver can sign it's request
     * 
     * @param object $tokens(->access_token)
     */
    function set_authentication($tokens) {
    	$this->oauth_token = $tokens->oauth_token;
        $this->oauth_token_secret = $tokens->oauth_token_secret;
    }
    
    /**
     * Make an api call to the service and sign it with the tokens given in set_authentication
     * 
     * @param string $endpoint_uri
     * @param array $params for passing all post/get parameters
     * @param enum(get,post) $method
     * @return string: returns all content of the http body returned on the request
     */
    public function api($endpoint, $params = array(), $method = 'get') {
    	$this->oauth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
    	$this->oauth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
    	
    	$code = $this->oauth->request(strtoupper($method),$this->oauth->url($endpoint), $params);
    	
    	if ($code == 200) {
    		return $this->oauth->response['response'];
    	} else {
    		return FALSE;
    	}
    }
}