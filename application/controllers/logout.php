<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * 
 * This class is the controller for all callbacks from all oauth providers like twitter, facebook, ...
 * depending on the service, the right authentication client is loaded to handle the identification
 * for matching with this applications users
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logout extends MY_Controller {
    
    function index() {
        $this->session->destroy();
        redirect('authenticate');
        
    }
}
