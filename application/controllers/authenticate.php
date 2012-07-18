<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Jens Segers <jens at iRail.be>
     * @author Hannes Van De Vreken <hannes at iRail.be>
     */
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class Authenticate extends CI_Controller {
        
        private $services = array('facebook','twitter','google','viking','foursquare');

        function index() {
            $plugs = new stdClass();
            foreach( $this->services as $service ){
                $plugs->$service = FALSE ;
            }
            $data = new stdClass();
            $data->plugs = $plugs ;
            $this->load->view('plugs',$data);
        }
    }
