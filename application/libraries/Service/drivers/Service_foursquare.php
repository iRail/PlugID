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
    
    function initialize($config = array()) {
        $this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['redirect_uri']);
    }
    
    function authorize() {
        $params = array('client_id' => $this->config['client_id'], 'redirect_uri' => $this->config['redirect_uri'], 'response_type' => 'code');
        redirect($this->url_authorize . '?' . http_build_query($params));
    }
    
    /**
     * Get access_token & ext_user_id
     */
    function callback($data) {
        $code = $data->code;
        if (!$code) {
            return FALSE;
        }
        
        // get access token
        $response = $this->oauth->getAccessToken($this->url_access_token, array('code' => $code));
        if ($response === FALSE) {
            return FALSE;
        }
        
        $this->access_token = $response->access_token;
        
        // get current user
        $user = $this->api('users/self');
        if ($user === FALSE) {
            return FALSE;
        }
        
        $auth = new stdClass();
        $auth->ext_user_id = (int) $user->response->user->id;
        $auth->access_token = $this->access_token;
        
        return $auth;
    }
    
    function set_authentication($tokens) {
        $this->access_token = $tokens->access_token;
    }
    
    public function api($endpoint, $params = array(), $method = 'get') {
    	$endpoint = $this->url_base . $endpoint;
    	$params['access_token'] = $this->access_token;
    	
    	return $this->oauth->fetch($endpoint, $params);
    }
}