<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 *
 * Distributes the clients requests to authorize this application to its network, given in $service
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Connect extends MY_Controller {
    
    function index($service) {
        // load plugin
        $this->load->driver('service', array('adapter' => $service));
        
        // let plugin do authorization
        $this->service->{$service}->authorize();
    }
}