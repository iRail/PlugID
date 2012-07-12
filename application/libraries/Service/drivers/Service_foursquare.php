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

class Service_foursquare extends Abstract_oauth2_service {
    
    private $service_name = 'foursquare';
    private $access_token;
    
    function __construct(){
        parent::__construct(); // important
        $this->ci->load->library('OAuth2_client', NULL, 'oauth2');
        $this->load_config($this->service_name, 'oauth2'); // file & optional subdir
        $this->setup_oauth_client_lib();
    }
    
    function authorize(){
        $params = array(
                'client_id' => $this->settings['client_id'],
                'redirect_uri' => $this->settings['callback_url'],
                'response_type' => 'code'
            );
        $this->build_and_redirect( $params );
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
        $access_token_resp = $this->ci->oauth2->get_access_token($code, $this->settings['client_id'], $this->settings['client_secret']);
        if( $access_token_resp === FALSE ){
            return FALSE ;
        }
        $access_token_resp = json_decode($access_token_resp);
        
        $this->access_token = $access_token_resp->access_token ;
        
        // Get users external id
        $json = $this->ci->oauth2->api('users/self',$this->access_token );
        $resp = json_decode($json);
        $access_token_resp->ext_user_id = (int)$resp->response->user->id;
        return $access_token_resp ;
    }
    
    function set_authentication( $tokens ){
        $this->access_token = $tokens->access_token ;
    }
}