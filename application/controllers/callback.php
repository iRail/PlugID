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
    
    /**
     * Main function which is automatically called when a callback is executed.
     * @param string $service_name The name of the service being called
     */
    function index($service_name) {
        // for checking & merging users
        $this->load->model('user_model');
        //Basically an include() for the service, found in application/libraries/Service/drivers/Service_{$service_name}.php
        $this->load->driver('service', array('adapter' => $service_name));
        if (!$this->service->is_valid($service_name)) {
            show_error('Error : ' . $service_name . ' is not a valid service name.');
        }
        
        // get user id and tokens from service
        $data = $this->service->$service_name->callback($this->input->get());
        
        if (!$data) {
            show_error('Error : Authentication of ' . $service_name .   ' failed');
        }
        
        // check if service is linked to existing user
        $user = $this->user_model->get_token_by_ext_id($service_name, $data->ext_user_id);
        
        // do some if else checks
        if (!$user && !$this->session->user_id) {
            // no user exists
            $user_id = $this->user_model->create()->user_id;
        } else if ($user && $this->session->user_id && $user->user_id != $this->session->user_id) {
            // merge 2 users
            $this->user_model->merge($user->user_id, $this->session->user_id);
            $user_id = $user->user_id;
        } else if ($user && !$this->session->user_id) {
            // connect to previous registered logged in user
            $user_id = $user->user_id;
        } else if (!$user && $this->session->user_id) {
            // connect to logged in user
            $user_id = $this->session->user_id;
        } else {
            // logged in and registered before
            $user_id = $this->session->user_id;
        }
        
        // log in user
        $this->session->user_id = (int) $user_id;
        
        // prep data
        $data->user_id = (int) $user_id;
        $data->service_type = $service_name;
        
        // save tokens
        $this->user_model->set_token((array) $data);
        
        // if $this->session->auth_request is set, handle auth_request (redirect)
        if ($this->session->auth_request) {
            $this->repeat_authorize();
        }
        
        if ($redirect = $this->session->redirect) {
        	unset($this->session->redirect);
            redirect($redirect);
        } else {
            redirect('profile/plugs');
        }
    }
    
    /**
     * This function redirects to the page where the user authorizes a client
     */
    private function repeat_authorize() {
        $auth_request = $this->session->auth_request;
        // we don't want this anymore in the future
        unset($this->session->auth_request);
        
        $url = 'oauth2/authorize';
        $params = array('client_id' => $auth_request->client_id, 'response_type' => $auth_request->response_type, 'redirect_uri' => $auth_request->redirect_uri);
        
        if ($auth_request->state) {
            $params['state'] = $auth_request->state;
        }
        
        $url .= '?' . http_build_query($params, NULL, '&');
        redirect($url);
    }

}
