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

    private $ci ;
    
    function index( $service_name ){
        $this->ci = &get_instance(); 
        
        // for checking & merging users
        $this->ci->load->model('user_model');
        $this->ci->load->library('session');
        
        // collect callback data
        $state       = $this->ci->input->get('state');
        
        $data = new stdClass();
        $data->code        = $this->ci->input->get('code');
        $data->oauth_token = $this->ci->input->get('oauth_token');
        
        // check state
        if( $state != $this->ci->session->state ){
            show_error('invalid_state');
        }
        
        // check params
        if( !$data->code && !$data->oauth_token ){
            show_error('invalid_response');
        }
        
        // load plugin
        $this->ci->load->driver('service', array('adapter' => $service_name ));
        
        // get user id from service
        $data = $this->ci->service->$service_name->complete_authorization((array)$data);
        
        // check if service is linked to existing user
        $user = $this->ci->user_model->get_user_from_service( $service_name, $data['ext_user_id']);
        if( !isset($user->user_id) ){
            // create user
            $user_id = $this->ci->user_model->create()->user_id;
        }else{
            // connect to logged in user
            if( $this->session->user && $user->user_id != $this->ci->session->user ){
                // merge 2 users
                $this->ci->user_model->merge( $user->user_id, $this->ci->session->user );
            }
            $user_id = $user->user_id;
        }
        
        // log in user
        $this->ci->session->user = (int)$user_id ;
        
        // be sure to add token to db
        // prep data
        $data['refresh_token'] = isset( $data['refresh_token'])? $data['refresh_token'] : NULL ;
        $data['ext_user_id'  ] = isset( $data['ext_user_id'  ])? $data['ext_user_id'  ] : NULL ;
        $data['user_id'] = (int)$user_id ;
        $data['service_type'] = $service_name;
        unset( $data['token_type'] );
        
        // set token
        $this->ci->user_model->set_token( $data );
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
        if( $this->ci->session->auth_request ){
            // build url for access_token request by external client
            
            // TODO
            // redirect();
        }
        
        //redirect('');
        echo 'done!' ;
    }

}
    
