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
        // for checking & merging users
        $this->load->model('user_model');
        
        // collect callback data
        $code = $this->input->get('code');
        $state = $this->input->get('state');
        
        // check state
        if( $state != $this->session->state ){
            show_error('invalid_state');
        }
        
        // load plugin
        $this->load->driver('service', array('adapter' => $service_name ));
        
        // get user id from service
        $ext_user_id = $this->service->$service_name->user_id();
        
        // check if service is linked to existing user
        $this->user_model->get_user_from_service( $service_name, $ext_user_id );
        
        // if yes: log-in as that user (and merge if needed)
        
        // if no: create user and link service
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
    }

}
    
