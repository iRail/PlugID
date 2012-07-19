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

class Service_foursquare extends Service_driver {
    
    private $oauth, $access_token;
    private $url_authorize = 'https://foursquare.com/oauth2/authorize';
    private $url_access_token = 'https://foursquare.com/oauth2/access_token';
    private $url_base = 'https://api.foursquare.com/v2/';
    
    /**
     * give config array with needed parameters like client_id, $client_secret etc.
     * @param array $config (loaded in service & passed)
     */
    function initialize($config = array()) {
        $this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['redirect_uri']);
    }
    
    /**
     * Redirect user to start authentication proces to authorize application to remote oauth provider
     */
    function authorize() {
        $params = array('client_id' => $this->config['client_id'], 'redirect_uri' => $this->config['redirect_uri'], 'response_type' => 'code', 'state' => $this->get_state());
        
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
        $error_message = 'Error authenticating with Google. Please try again later. Technical detail for our monkeys: ';
        // check code
        if (!isset($data['code'])) {
            show_error($error_message . 'Invalid request: no code returned');
        }
        $code = $data['code'];
        
        // check state
        if (!isset($data['state'])) {
            show_error($error_message . 'Invalid request: no state returned');
        }
        
        if ($state != $this->session->state) {
            show_error($error_message . 'Invalid state returned');
        }
        unset($this->session->state);
        
        // get access token
        $response = $this->oauth->getAccessToken($this->url_access_token, array('code' => $code));
        
        // response valid?
        $response = json_decode($response);
        if (is_null($response) || !isset($response->access_token)) {
            show_error($error_message . 'Access token request failed');
        }
        
        // save some stuff, we'll need it to sign our first api call
        $this->access_token = $response->access_token;
        // get current user
        $user = $this->api('users/self');
        
        // valid json response?
        $user = json_decode($user);
        if (is_null($user) || !isset($user->response->user->id)) {
            show_error($error_message . "Error while retrieving UserID from Foursquare");
        }
        
        $auth = new stdClass();
        $auth->ext_user_id = (int) $user->response->user->id;
        $auth->access_token = $this->access_token;
        
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
        $endpoint = rtrim($this->url_base, '/') . '/' . trim($endpoint, '/');
        $params['oauth_token'] = $this->access_token;
        return $this->oauth->fetch($endpoint, $params, $method);
    }
}