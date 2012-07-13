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
    
    /**
     * give config array with needed parameters like client_id, $client_secret etc.
     * @param array $config (loaded in service & passed)
     */
	function initialize($config = array()){
		$this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['$redirect_uri']);
		$this->config = $config;
	}
    
    /**
     * Initial function to start authentication proces. Redirect user to oauth provider's authenticate url
     * no param - no return
     */
    function authorize(){
        $params = array(
                'client_id' => $this->config['client_id'],
                'redirect_uri' => $this->config['redirect_uri'],
                'response_type' => 'code'
            );
        redirect( $this->config['url_authorize'] . '?' . http_build_query($params));
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
    function callback( $callback_data ){
        $code = $callback_data->code ;
        if( !$code ){
            return FALSE;
        }
        // Access Token Response
        $access_token_resp = $this->oauth->getAccessToken('https://foursquare.com/oauth2/access_token',$code);
        if( $access_token_resp === FALSE ){
            return FALSE ;
        }     
        $this->access_token = $access_token_resp->access_token ;
        
        // Get users external id
        $fetch_response = $this->oauth->fetch('https://api.foursquare.com/v2/users/self',$this->access_token );
        if( $fetch_response === FALSE ){
        	return FALSE ;
        }
        $resp = json_decode($this->oauth->getLastResponse());
        $access_token_resp->ext_user_id = (int)$resp->response->user->id;
        return $access_token_resp ;
    }
    
    /**
     * This function is used to give the tokens to the driver. With this, the driver can sign it's request
     * 
     * @param object $tokens(->access_token)
     */
    function set_authentication( $tokens ){
        $this->access_token = $tokens->access_token ;
    }
    
    /**
     * Make an api call to the service and sign it with the tokens given in set_authentication
     * 
     * @param string $endpoint_uri
     * @param array $params for passing all post/get parameters
     * @param enum(get,post) $method
     * @return string: returns all content of the http body returned on the request
     */
    public function api( $endpoint_uri, $params = array(), $method = 'get' ){
    	$url = 'https://api.foursquare.com/v2/' . trim($endpoint_uri, '/');
    	$fetch_response = $this->oauth->fetch($url, $this->access_token );
    	if( $fetch_response === FALSE ){
    		return FALSE ;
    	}
        return $this->oauth->getLastResponse();
    }
}