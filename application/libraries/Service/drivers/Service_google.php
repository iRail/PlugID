
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

class Service_google extends Service_driver {
    
    private $oauth, $access_token, $refresh_token, $scope;
    private $scope_delim = ' '; // will get url encoded with '+'
    private $url_authorize = 'https://accounts.google.com/o/oauth2/auth';
    private $url_access_token = 'https://accounts.google.com/o/oauth2/token';
    private $url_base = 'https://www.googleapis.com/oauth2/v1/';
    
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
                        'response_type' => 'code',
                        'scope' => $this->scope,
                        'state' => $this->get_state(),
                        'access_type' => 'offline',
                        'approval_prompt' => 'force' // to get refresh token
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
        $error = new stdClass();
        $code = $data->code;
        if (!$code) {
            $error->error = 'Invalid request: no code returned';
        }
        $state = $data->state;
        if (!$state) {
            $error->error = 'Invalid request: no state returned';
        }
        if ($state != $this->ci->session->state) {
            $error->error = 'Invalid state returned';
        }
        unset($this->ci->session->state);
        if (isset($error->error)) {
            return $error;
        }
        
        // get access token
        $response = $this->oauth->getAccessToken($this->url_access_token, array('code' => $code) );
        // response valid?
        $response = json_decode($response);
        if (is_null($response)) {
            return FALSE;
        }
        // save some stuff, we'll need it to sign our first api call
        $this->access_token = $response->access_token;
        $this->refresh_token = $response->refresh_token;
        
        // get current user
        $user = $this->api('tokeninfo');
        // valid json response?
        $user = json_decode($user);
        if ( is_null($user) || !isset($user->user_id) ){
            return FALSE;
        }
        
        $auth = new stdClass();
        $auth->ext_user_id = $user->user_id;
        $auth->access_token = $this->access_token;
        $auth->refresh_token = $this->refresh_token;
        $auth->expires = $response->expires_in + time();
        
        return $auth;
    }
    
    /**
     * This function is used to give the tokens to the driver. With this, the driver can sign it's request
     * 
     * @param object $tokens(->access_token)
     */
    function set_authentication($tokens) {
        $this->access_token = $tokens->access_token;
        if( $tokens->expires < time() ){
            $return = $this->oauth->refreshAccessToken( $this->url_access_token, $this->refresh_token );
            var_dump( $return );
        }
        $this->refresh_token = $tokens->refresh_token;
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
        
        return $this->oauth->fetch($endpoint, $params);
    }
}