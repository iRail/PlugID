<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * @author Koen De Groote <koen at iRail.be>s
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_viking extends Abstract_service {
    
    function __construct(){
        parent::__construct('viking');
        $this->service_name = 'viking';
        $this->ci->load->library('OAuth2_client', array('service' => $this->service_name), $this->service_name);
    }
    
    function user_id() {
        $bearer_token = $this->ci->{$this->service_name}->token();
        $json = $this->ci->{$this->service_name}->api('users/', array('user_id' => '123','bearer_token' => $bearer_token));
        $result = json_decode($json);
        return $result->response->id;
    }
}