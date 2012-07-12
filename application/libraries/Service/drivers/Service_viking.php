<?php

/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Koen De Groote <koen at iRail.be>
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_viking extends Service_driver {
    
    private $service_name = 'viking';
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
    function identify($callback_data) {
        $code = $callback_data->code;
        // Access Token Response
        $access_token_resp = $this->ci->{$this->service_name}->get_access_token($code);

        if ($access_token_resp !== FALSE) {
            // get users external id
            $json = $this->ci->{$this->service_name}->api('users/', array('bearer_token' => $bearer_token));
            $resp = json_decode($json);
            $access_token_resp->ext_user_id = (int) $resp->response->id;
            return $access_token_resp;
        } else {
            return FALSE;
        }
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