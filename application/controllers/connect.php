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
    
    function index( $service ){
        // load plugin
        
        // get authorize url from plugin
        $authorize_url = 'http://www.youtube.com/watch?v=dQw4w9WgXcQ';
        
        // redirect to plugin
        redirect( $authorize_url );
    }
}