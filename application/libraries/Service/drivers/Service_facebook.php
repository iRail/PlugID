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

    class Service_facebook extends Abstract_service {

        function __construct(){
        parent::__construct('facebook');
        $this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
    }
    
    function identify( $callback_data ){
        $code = $callback_data->code ;
        // Access Token Response
        $access_token_resp = $this->ci->{$this->service_name}->get_access_token($code);
        
        if($code !== FALSE){
            // get users external id
            $json = $this->ci->{$this->service_name}->api('accounts','');
            $resp = json_decode($json);
            $access_token_resp->ext_user_id = (int)$resp->response->user->id;
            return $access_token_resp;
        }else{
            return FALSE;
        }
    }
    
    function set_identification( $config ){
        $this->ci->{$this->service_name}->set_authentication( $config );
    }

    }