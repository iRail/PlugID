<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Handles the authorize page
 * The user will be redirected to /authorize, and must click on allow to give the permission to the client.
 * Minimal parameters: Client_id and response_type
 * Optional parameters: Redirect_uri and state
 */
class Authorize extends CI_Controller {

    function index() {
        $this->ci = &get_instance();
        $this->ci->load->model('user_model');
        $this->ci->load->model('code_model');
        
        // required
        $client_id = $this->input->get('client_id');
        $response_type = $this->input->get('response_type');
        
        // optional
        $redirect_uri = $this->input->get('redirect_uri');
        $state = $this->input->get('state');
        
        // invalid_request
        if (!($client_id && $response_type)) {
            show_error('Error: this request is invalid. The client_id and the response_type parameters have to be included.  
                        Please go back to the site or application that sent you here and try again.');
        }
        
        // check on client
        $this->ci->load->model('client_model');
        $client = $this->ci->client_model->get($client_id);
        
        // client does not exist
        if (!isset($client->client_id)) {
            show_error('Error: the site or application that sent you here is not recognized.');
        }
        
        // optional callback
        if ($redirect_uri !== FALSE) {
            if (strtok($redirect_uri,'?') != $client->redirect_uri) { // allow state param set, etc...
                // Wrong redirect_uri
                show_error('Error: the redirect URI provided is not associated with the site or applicatioin that sent you here. Please try again.');
            }
        } else {
            $redirect_uri = $client->redirect_uri;
        }
        
        // unsupported_response_type
        if ($response_type != 'code') {
            show_error('Error: this request is invalid. The response_type parameter has to be code.  
                        Please go back to the site or application that sent you here and try again.');
        }
        
        // check if user is actually signed in
        $user_id = $this->session->userdata('user_id');
        
        // so, by now, all invalid request should be caught
        
        // it's a no go
        if ($user_id === FALSE || count( $this->user_model->get_tokens( $user_id )) == 0 ) {
            // collect request data
            $auth_request = new stdClass();
            
            $auth_request->client_id = $client_id;
            $auth_request->response_type = $response_type;
            
            $auth_request->redirect_uri = $redirect_uri;
            $auth_request->state = $state;
            
            // save request data to return later on
            unset($this->session->auth_request);
            $this->session->auth_request = $auth_request;
            
            // get the user to log in
            redirect('authenticate');
        }
        
        $is_authorized = $this->ci->user_model->is_client_authorized($user_id, $client_id);
        $is_allowed = $this->input->post('allow') !== FALSE;
        // Allow button clicked OR
        if ($is_allowed || $is_authorized) {
            // Save allowance
            $this->ci->user_model->authorize_client($user_id, $client_id);
            
            // Generate code
            $code = $this->ci->code_model->create($client_id, $user_id);
            
            // Generate callback url
            $params = array();
            $params['code'] = $code;
            if ($state !== FALSE) {
                $params['state'] = $state;
            }
            
            $redirect_uri .= (strpos($redirect_uri, '?') ? '&' : '?') . http_build_query($params);
            // Redirect back to user website
            redirect($redirect_uri);
        } else {
            // show access screen
            $data = array();
            $data['client'] = $client->name;
            $this->load->view('header.tpl');
            $this->load->view('authorize', $data);
            $this->load->view('footer.tpl');
        }
    }
}