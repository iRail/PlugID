<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Access_token extends CI_Controller {
    
    function index() {
        $data = array();
        
        // Required
        $grant_type = $this->input->post('grant_type');
        $code = $this->input->post('code');
        $redirect_uri = $this->input->post('redirect_uri');
        $client_id = $this->input->post('client_id');
        
        // Client secret from basic auth header OR post param
        $client_secret = $this->input->get_request_header('Authorization');
        if ($client_secret !== FALSE && preg_match('/^Basic\ (\w{32})$/', $client_secret, $matches)) {
            $client_secret = $matches[1];
        } else {
            $client_secret = $this->input->post('client_secret');
        }
        
        $this->load->model('code_model');
        $this->load->model('client_model');
        
        // Client_secret must be given either way
        if (!$client_secret || !$grant_type || !$code || !$client_id || !$redirect_uri) {
            $data['error'] = 'invalid_request';
        
        // Hard-coded: 'grant-type' must be 'authorization_code'
        } else if ($grant_type != 'authorization_code') {
            $data['error'] = 'unsupported_grant_type'; //'invalid_grant' ;
        

        // Authenticate client
        } else if (!$this->client_model->validate_secret($client_id, $client_secret)) {
            $data['error'] = 'invalid_client';
        
        // Validate code
        } else if (!$this->client_model->validate_redirect_uri($client_id, $redirect_uri)) {
            $data['error'] = '';
        
        // Validate code
        } else if (! $code = $this->code_model->is_valid($code, $client_id)) {
            $data['error'] = 'unauthorized_client';
        
        } else {
            // Hooray! Give the lad a token!
            $this->load->model('access_token_model');
            $result = $this->access_token_model->create($client_id, $code->user_id);
            $data['access_token'] = $result->access_token;
            // Removed. Authentication header OAuhth (case insensitive) or ?oauth_token= are allowed
            //$data['token_type'] = 'Bearer';
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }

}