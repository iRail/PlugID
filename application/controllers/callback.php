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

class Callback extends CI_Controller {

    function index( $service_name ){        
        // for checking & merging users
        $this->load->model('user_model');
        $this->load->library('session');
        
        // collect callback data
        $state = $this->input->get('state');
        
        $data = new stdClass();
        $data->code = $this->input->get('code');
        $data->oauth_token = $this->input->get('oauth_token');
        
        // check state
        if ($state != $this->session->state) {
            show_error('invalid_state');
        }
        
        // empty this
        unset($this->session->state);
        
        // check params
        if (!$data->code && !$data->oauth_token) {
            show_error('invalid_response');
        }
        
        // load plugin
        $this->load->driver('service', array('adapter' => $service_name));
        
        // get user id from service
        if (!$data = $this->service->$service_name->identify($data)) {
            show_error('authentication failed');
        }
        
        $data->service_type = $service_name;
        
        // check if service is linked to existing user
        $user = $this->user_model->get_token_by_ext_id($data->service_type, $data->ext_user_id);
        if (!isset($user->user_id)) {
            // create user
            $user_id = $this->user_model->create()->user_id;
        } else {
            // connect to logged in user
            if ($this->session->user && $user->user_id != $this->session->user) {
                // merge 2 users
                $this->user_model->merge($user->user_id, $this->session->user);
            }
            $user_id = $user->user_id;
        }
        
        // log in user
        $this->session->user = (int) $user_id;
        
        // be sure to add token to db
        // prep data
        $data->user_id = (int) $user_id;
        
        // set token
        $this->user_model->set_token((array) $data);
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
        $auth_request = $this->session->auth_request;
        unset($this->session->auth_request);
        
        if( $auth_request ){
            $url  = 'oauth2/authorize' ;
            $params = array(
                        'client_id'     => $auth_request->client_id,
                        'response_type' => $auth_request->response_type,
                        'redirect_uri'  => $auth_request->redirect_uri
                      );
            
            if ($auth_request->state) {
                $params['state'] = $auth_request->state;
            }
            
            $url .= '?' . http_build_query($params, NULL, '&');
            redirect($url);
        }
        
        redirect('');
    }

}