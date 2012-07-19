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
    
    private $url_authorize = 'https://api.twitter.com/oauth/authorize';
    private $url_request_token = 'https://api.twitter.com/oauth/request_token';
    private $url_access_token = 'https://api.twitter.com/oauth/access_token';
    private $url_base = 'https://api.twitter.com/1/';
    
    function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->library('Session');
    }
    
    /**
     * Construct an object of the wrapper class OAuth1 (which will construct an object of tmhOAuth)
     * give config array with needed parameters like client_id, $client_secret etc.
     * @param array $config (loaded in service & passed)
     */
    function initialize($config = array()) {
        $this->oauth = new OAuth1($config['consumer_key'], $config['consumer_secret']);
    }
    
    /**
     * Step 1: Get a request token with getRequestToken
     * Response: oauth_token and oauth_token_secret: store this in the session
     * 
     * Step 2: Redirect to the authorize endpoint of twitter with parameter oauth_token
     */
    function authorize() {
        $request_token = $this->oauth->getRequestToken($this->url_request_token, array('oauth_callback' => $this->config['redirect_uri']));
        
        if (!$request_token) {
            show_error('Invalid request: no request token returned');
        }
        
        $this->ci->session->twitter_token = $request_token;
        
        $params = array();
        $params['oauth_token'] = $request_token['oauth_token'];
        redirect($this->url_authorize . '?' . http_build_query($params));
    }
    
    /**
     * Function AFTER authorize
     * Step 1 : Get the oauth_token and oauth_verifier from the callback data, and the secret from the session
     * Step 2 : Obtain an access token with these parameters (GetAccessToken)
     * Response: oauth_token and oauth_token_secret
     * Step 3: Verify the credentials with oauth_token and oauth_token_secret an extract the user id.
     * 
     * @param array data contains oauth_token and oauth_verifier to request access token.
     * @return  FALSE on failure
     * object->ext_user_id
     * object->oauth_token
     * object->oauth_token_secret
     */
    function callback($data) {
    	$error_message = 'Error authenticating with Twitter. Please try again later. Technical detail for our monkeys: ';
        if (!isset($data['oauth_token'])) {
            show_error('Invalid request: no oauth token returned');
        }
        
        if (!isset($data['oauth_verifier'])) {
            show_error($error_message . 'Invalid request: no oauth verifier returned');
        }
        
        $params['oauth_token'] = $this->ci->session->twitter_token['oauth_token'];
        $params['oauth_token_secret'] = $this->ci->session->twitter_token['oauth_token_secret'];
        $params['oauth_verifier'] = $data['oauth_verifier'];
        
        $access_token = $this->oauth->getAccessToken($this->url_access_token, $params);
        if (!$access_token) {
            show_error($error_message . 'Access token request failed');
        }
        
        unset($this->ci->session->twitter_token);
        
        $this->oauth_token = $access_token['oauth_token'];
        $this->oauth_token_secret = $access_token['oauth_token_secret'];
        
        $user = $this->api('account/verify_credentials');
        if (!$user) {
            show_error($error_message . 'Failed to get external user id');
        }
        
        $auth = new stdClass();
        $auth->ext_user_id = (int) $user->id;
        $auth->oauth_token = $access_token['oauth_token'];
        $auth->oauth_token_secret = $access_token['oauth_token_secret'];
        
        return $auth;
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
     * @param string $endpoint: the endpoint of the API.
     * @param array $params for passing all post/get parameters
     * @param enum(get,post) $method
     * @return string: returns all content of the http body returned on the request
     * 					or FALSE
     */
    public function api($endpoint, $params = array(), $method = 'get') {
        $endpoint = rtrim($this->url_base, '/') . '/' . trim($endpoint, '/') . '.json';
        
        $params['oauth_token'] = $this->oauth_token;
        $params['oauth_token_secret'] = $this->oauth_token_secret;
        
        return json_decode($this->oauth->fetch($endpoint, $params, $method));
    }
}