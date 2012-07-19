<?php

/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Lennart Martens <lennart at iRail.be>
 * @author Koen De Groote <koen at iRail.be>
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_facebook extends Service_driver {
    
    private $oauth, $access_token, $scope;
    private $scope_delim = ',';
    private $url_authorize = 'https://www.facebook.com/dialog/oauth';
    private $url_access_token = 'https://graph.facebook.com/oauth/access_token';
    private $url_base = 'https://graph.facebook.com/';
    
    /**
     * give config array with needed parameters like client_id, $client_secret etc.
     * @param array $config (loaded in service & passed)
     */
    function initialize($config = array()) {
        $this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['redirect_uri']);
        $this->scope = implode($this->scope_delim, $config['scope']);
    }
    
    /**
     * Redirect user to start authentication proces to authorize application to remote oauth provider
     */
    function authorize() {
        $params = array('client_id' => $this->config['client_id'], 
                        'redirect_uri' => $this->config['redirect_uri'], 
                      //'response_type' => 'code',
                        'state' => $this->get_state(),
                        'scope' => $this->scope
                        );
                        
        redirect($this->url_authorize . '?' . http_build_query($params));
    }
    
    /**
     * Get access_token & ext_user_id
     * 
     * @param oject $callback_data contains ->code to finish authentication
     * @return  FALSE on failure
     *          object
     */
    function callback($data) {
    	$error_message = 'Error authenticating with Facebook. Please try again later. Technical detail for our monkeys: ';
        // check code
        if (!isset($data['code'])) {
            show_error($error_message . 'Invalid request: no code returned');
        }
        $code = $data['code'];
        
        // check state
        if (!isset($data['state'])) {
            show_error($error_message . 'Invalid request: no state returned');
        }
        $state = $data['state'];
        $session_state = $this->ci->session->state ;
        
        unset($this->ci->session->state);
        if ($state != $session_state) {
            show_error($error_message . 'Invalid state returned');
        }
        
        // get access token
        $response = $this->oauth->getAccessToken($this->url_access_token, array('code' => $code));
        
        // response valid?
        $resp_parts = explode('&',$response);
        $response = new stdClass();
        foreach( $resp_parts as &$var ){
            list($one,$two) = explode('=',$var);
            $response->$one = $two ;
        }
        
        if( !isset($response->access_token)){
            show_error($error_message . 'Access token request failed');
        }
        
        // save some stuff, we'll need it to sign our first api call
        $this->access_token = $response->access_token;
        
        // get current user
        $user = $this->api('me');
        
        // valid json response?
        $user = json_decode($user);
        if( is_null($user) || !isset($user->id) ){
            show_error( $error_message . 'Failed to get external user id');
        }
        
        $auth = new stdClass();
        $auth->ext_user_id = (int) $user->id;
        $auth->access_token = $this->access_token;
        $auth->expires = time() + (int)$response->expires ;
        
        return $auth;
    }
    
    /**
     * This function is used to give the tokens to the driver. With this, the driver can sign it's request
     * 
     * @param object $tokens(->access_token)
     */
    function set_authentication($tokens) {
        $this->access_token = $tokens->access_token;
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
        $endpoint = rtrim($this->url_base,'/') . '/' . trim($endpoint,'/');
        $params['oauth_token'] = $this->access_token;
        
        return $this->oauth->fetch($endpoint, $params, $method);
    }
}