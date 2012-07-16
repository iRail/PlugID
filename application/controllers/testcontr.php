<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class testcontr extends CI_Controller {
    
    function index() {
        unset( $this->session->user) ;
        $this->session->user_id = 1278543110 ;
    }
}