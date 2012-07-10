<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class login extends CI_Controller {
    
    function index() {
        if( $this->session->user ){
            redirect( '' );
        }else{
            $this->load->view('login');
        }
    }
}