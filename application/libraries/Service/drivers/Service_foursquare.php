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

class Service_foursquare extends Abstract_service {
    
    function __construct(){
        parent::__construct('foursquare');
    }
    
    function user_id() {
        $results = $this->ci->{$service_name}->api('users',array('user_id' => '123'));
        var_dump($results);
    }
}