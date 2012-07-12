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

abstract class Service_default extends CI_Driver {
    
    protected $ci ;
    protected $settings ;
    
    function __construct(){
        $this->ci = &get_instance();
        $this->ci->load->library('curl');
    }
    
    protected function load_config($name, $conf_dir = NULL) {
        $file = is_null($conf_dir) ? '' : rtrim($conf_dir, '/') . '/';
        $file .= $name;
        $this->ci->config->load($file, TRUE);
        $this->settings = $this->ci->config->item($file);
    }
    
    public function api( $endpoint_uri, $params = array(), $method = 'get' ){
        $endpoint_uri = $this->settings['url_api_base'] . $endpoint_uri ;
        return $this->ci->curl->{$method}( $endpoint_uri, $params );
    }
}