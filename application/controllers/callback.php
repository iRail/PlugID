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
        $this->load->driver('service', array('adapter' => $service_name));
        if( !isset($this->service->$service_name) ){
            show_error( $service_name .' is not a valid service name.');
        }
        
        // check state
        if ($this->input->get('state') != $this->session->state) {
            show_error('invalid_state');
        }
        unset($this->session->state);
        
        // collect callback data
        $data = new stdClass();
        $data->code           = $this->input->get('code'); // OAuth2
        $data->oauth_token    = $this->input->get('oauth_token'); // OAuth1
        $data->oauth_verifier = $this->input->get('oauth_verifier'); // OAuth1.0a
        
        // one of them has to be filled in, at least
        if (!$data->code && !$data->oauth_token) {
            show_error('invalid_response');
        }
        
        // call is valid!
        
        // get user id and tokens from service
        if (!$data = $this->service->$service_name->callback($data)) {
            show_error('authentication failed');
        }
        
        // check if service is linked to existing user
        $user = $this->user_model->get_token_by_ext_id($service_name, $data->ext_user_id);
        
        // do some if else checks
        if (!isset($user->user_id) && !$this->session->user) {
            // no user exists
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
        // prep data
        $data->user_id = (int) $user_id;
        $data->service_type = $service_name;
        // save tokens
        $this->user_model->set_token((array) $data);
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
        if( $this->session->auth_request ){
            $this->repeat_authorize();
        }
        
        redirect('');
    }
    
    /**
     * This function redirects to the page where the user authorizes a client
     */
    private function repeat_authorize(){
        $auth_request = $this->session->auth_request;
        // we don't want this anymore in the future
        unset($this->session->auth_request);
        
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

}
