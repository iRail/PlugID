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
    
    private $oauth, $config;
    
    private $auth_url = 
    
    function __construct(){
        parent::__construct();
        
        $this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['$redirect_uri']);
		$this->config = $config;
    }
    
    /*function callback( $callback_data ){
        $code = $callback_data->code ;
        // Access Token Response
        $access_token_resp = $this->ci->{$this->service_name}->get_access_token($code);
        
        if($access_token_resp !== FALSE){
            // get users external id
            $json = $this->ci->{$this->service_name}->api('me','');
            $resp = json_decode($json);
            $access_token_resp->ext_user_id = (int)$resp->id;
            //We don't keep expires in the database
            unset($access_token_resp->expires);
            return $access_token_resp;
        }else{
            return FALSE;
        }
    }
    */
    
    function authorize(){
    	$params = array(
                'client_id' => $this->config['client_id'],
                'redirect_uri' => $this->config['callback_url'],
                'response_type' => 'code'
         );
        $this->build_and_redirect( $params );
    }
    
    function callback( $callback_data ){
        
    }
    
    function set_authentication( $config ){
        
    }
}