<?php
/**
 * Note: the api base url of Twitter: https://api.twitter.com/1/
 * 
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Lennart Martens <lennart at iRail.be>
 * 
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_twitter extends Abstract_service {
    
    function __construct(){
        parent::__construct('twitter');
        $this->ci->load->library('OAuth1_client', array('service' => $this->service_name), $this->service_name);
    }
    
    function identify( $callback_data ){
    	//Get the oauth_verifier from the callback_data
        $oauth_verifier = $callback_data->oauth_verifier ;
               
        // Access Token Response
        $access_token_resp = $this->ci->{$this->service_name}->get_access_token($oauth_verifier);
         
        if( $access_token_resp !== FALSE ){
            // get users external id
            $json = $this->ci->{$this->service_name}->api('account/verify_credentials.json');
            $resp = json_decode($json);
            $access_token_resp->ext_user_id = (int)$resp->id;
            return $access_token_resp ;
        }else{
            return FALSE ;
        }
    }
    
    function set_identification( $config ){
        $this->ci->{$this->service_name}->set_authentication( $config );
    }
    
    function authorize()
    {
    	//Get request_token
    	$response = array();
    	$response = $this->ci->{$this->service_name}->get_request_token();
    	if($response != FALSE)
    	{
    		//Set the oauth_token and oauth_token_secret
    		$this->ci->{$this->service_name}->set_authentication($response);
    		//Redirect to the authorize page
    		redirect($this->ci->{$this->service_name}->authorize());
    	}
    	else {
    		//Returned an error
    		return FALSE;
    	}
    	
    	
    }
}