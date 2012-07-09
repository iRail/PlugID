<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 * 
 * Retrieve callback attributes from authorisation
 */
 
class callback extends CI_Controller {

    function index( $service_name ){
        // 
        $this->load->model('user_model');
        
        // collect callback data
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        
        // check state
        // if( $state == $this->session->state ){}
        
        // load plugin
        $this->load->driver('service', array('adapter' => $service_name ));
        echo $this->service->$service_name->user_id(); exit ;
        
        // get user id from service
        
        // check if service is linked to existing user
        
        // if yes: log-in as that user (and merge if needed)
        
        // if no: create user and link service
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
    }

}
    
