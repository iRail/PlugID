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
    
    private $service_name = 'foursquare';
    private $access_token;
    private $oauth;
    private $config;
    
	function initialize($config = array()){
		$this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['redirect_uri']);
		$this->config = $config;
	}
    
    function authorize(){
        $params = array(
                'client_id' => $this->config['client_id'],
                'redirect_uri' => $this->config['redirect_uri'],
                'response_type' => 'code'
            );
        redirect('https://foursquare.com/oauth2/authenticate' . '?' . http_build_query($params));
    }
    
    /**
     * Get access_token & ext_user_id
     */
    function callback( $callback_data ){
        $code = $callback_data->code ;
        if( !$code ){
            return FALSE;
        }
        // Access Token Response
        $access_token_resp = $this->oauth->getAccessToken('https://foursquare.com/oauth2/access_token',array('code' => $code));
        if( $access_token_resp === FALSE ){
            return FALSE ;
        }     
        $this->access_token = $access_token_resp->access_token ;
        
        // Get users external id
        $fetch_response = $this->oauth->fetch('https://api.foursquare.com/v2/users/self',array('access_token' => $this->access_token));
        if( $fetch_response === FALSE ){
        	return FALSE ;
        }
        $resp = json_decode($this->oauth->getLastResponse());
        $access_token_resp->ext_user_id = (int)$resp->response->user->id;
        return $access_token_resp ;
    }
    
    function set_authentication( $tokens ){
        $this->access_token = $tokens->access_token ;
    }
    
    public function api( $endpoint_uri, $params = array(), $method = 'get' ){
    	$url = 'https://api.foursquare.com/v2/' . trim($endpoint_uri, '/');
    	$fetch_response = $this->oauth->fetch($url, array('access_token' => $this->access_token));
    	if( $fetch_response === FALSE ){
    		return FALSE ;
    	}
        return $this->oauth->getLastResponse();
    }
}