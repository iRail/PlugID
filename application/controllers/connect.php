<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 *
 * Distributes the clients requests to authorize this application to it's network, given in $service
 */

class connect extends CI_Controller {
    
    function index( $service_name ){
        // load plugin
        $this->load->driver('service', array('adapter' => $service_name ));
        
        // get authorize url from plugin
        $authorize_url = $this->service->foursquare->get_authorization_ur();
        
        // redirect to plugin
        redirect( $authorize_url );
    }
}