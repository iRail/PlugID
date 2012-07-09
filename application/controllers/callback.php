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

    function index( $service ){
        // 
        $this->load->model('user_model');
        
        // collect callback data
        $this->input->get('code');
        
        // load plugin
        $this->load->library('OAuth2_client', array('service' => $service) );
        
        // get user id from service
        
        
        // check if service is linked to existing user
        //$this->user_model->
        
        // if yes: log-in as that user (and merge if needed)
        
        // if no: create user and link service
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
    }

}
    
