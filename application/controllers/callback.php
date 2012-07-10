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
        $state       = $this->input->get('state');
        
        $data = new stdClass();
        $data->code        = $this->input->get('code');
        $data->oauth_token = $this->input->get('oauth_token');
        
        // check state
        if( $state != $this->session->state ){
            show_error('invalid_state');
        }
        
        // check params
        if( !$data->code && !$data->oauth_token ){
            show_error('invalid_response');
        }
        
        // load plugin
        $this->load->driver('service', array('adapter' => $service_name ));
        
        // get user id from service
        $data = $this->service->$service_name->complete_authorization((array)$data);
        /*
        // check if service is linked to existing user
        $user = $this->user_model->get_user_from_service( $service_name, $data['ext_user_id']);
        var_dump( $user ); exit ;
        if( !$user ){
            // create user
            $user_id = $this->user_model->create()->user_id;
        }else{
            // connect to logged in user
            if( !$this->session->user ){
                // log user in
                $user_id = $user['user_id'] ;
            }else if( $user['user_id'] != $this->session->user ){
                // merge 2 users
                $this->user_model->merge( $user['user_id'], $this->session->user );
                $user_id = $user['user_id']; // important it is the first of 2 params from above!
            }else{
                // go get some rest, no work to be done here
            }
        }
        
        // log in
        $this->session->user_id = $user_id ;
        
        // fill er up anyway
        $data['refresh_token'] = isset( $data['refresh_token'])? $data['refresh_token'] : NULL ;
        $data['ext_user_id'  ] = isset( $data['ext_user_id'  ])? $data['ext_user_id'  ] : NULL ;
        
        // set token
        $this->user_model->set_token( $user_id, $service_name, $data['access_token'], $data['refresh_token'], $data['ext_user_id'] )
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
        if( $this->session->auth_request ){
            // build url for access_token request by external client
            
            // TODO
            // redirect();
        }
        
        redirect('');*/
    }

}
    
