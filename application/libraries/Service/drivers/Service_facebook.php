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
    
    private $oauth, $config, $access_token;
    
    private $url_authorize = 'https://www.facebook.com/dialog/oauth';
    private $url_access_token = 'https://graph.facebook.com/oauth/access_token';
    private $url_base = 'https://graph.facebook.com/';
    
    function __construct() {
        parent::__construct();
        $this->oauth = new OAuth2($config['client_id'], $config['client_secret'], $config['redirect_uri']);
    }
    
    function authorize() {
        $params = array('client_id' => $this->config['client_id'], 'redirect_uri' => $this->config['callback_url'], 'response_type' => 'code');
        redirect($this->url_authorize . '?' . http_build_query($params));
    }
    
    function callback($data) {
        $token = $this->oauth->getAccessToken($this->url_access_token, $data['code']);
    }
    
    function set_authentication($config) {
        $this->access_token = $tokens->access_token;
    }
    
    function api($endpoint, $params = array(), $method = 'get') {
        $endpoint = $this->url_base . $endpoint;
        $data = json_decode($this->oauth->fetch($endpoint, $params));
        return $data;
    }
}