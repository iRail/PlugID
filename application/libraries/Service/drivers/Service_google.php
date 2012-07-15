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

/*
$config['url_authorize']    = 'https://accounts.google.com/o/oauth2/auth';
$config['url_access_token'] = 'https://accounts.google.com/o/oauth2/token';
$config['url_api_base']     = 'https://www.googleapis.com/oauth2/v1/';
 */

class Service_google extends Service_driver {

	private $service_name = 'google';
    private $access_token = NULL;
    
    function __construct(){
        parent::__construct(); // important
        $this->ci->load->library('OAuth2_client', NULL, 'oauth2');
        $this->load_config($this->service_name, 'oauth2'); // file & optional subdir
        $this->setup_oauth_client_lib();
    }
    
    private function setup_oauth_client_lib(){
        $this->ci->oauth2->callback_url     = $settings['callback_url'];
        $this->ci->oauth2->url_authorize    = $settings['url_authorize'];
        $this->ci->oauth2->url_access_token = $settings['url_access_token'];
        $this->ci->oauth2->url_api_base     = $settings['url_api_base'];
    }
    /*
	function callback( $callback_data ){
		//Getting user_id and verifying the token -> see 
		//https://developers.google.com/accounts/docs/OAuth2UserAgent#validatetoken
		$json = $this->ci->{$this->service_name}->api('oauth2/v1/tokeninfo');
		$result = json_decode($json);
		if($result == NULL || isset($result->error)){
			return FALSE;
		}
		return $result->user_id;
	}
   */
    function authorize(){
        $params = array(
                'client_id' => $this->settings['client_id'],
                'redirect_uri' => $this->settings['callback_url'],
                'response_type' => 'code'
            );
        $this->build_and_redirect( $params );
    }
    
    function callback( $callback_data ){
        
    }
    
    function set_authentication( $config ){
        
    }
}