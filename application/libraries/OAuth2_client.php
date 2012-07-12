<?php     if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Jens Segers <jens at iRail.be>
     * @author Hannes Van De Vreken <hannes at iRail.be>
     */
    class OAuth2_client {
        
        private $ci;
        
        public $callback_url     = '';
        public $url_authorize    = '';
        public $url_access_token = '';
        public $url_api_base     = '';
        
        function __construct() {
            $this->ci = &get_instance();
            $this->ci->load->library('curl');
        }
        
        /**
         * Request access token from code
         * @param string $code
         * @param array $options
         */
        public function get_access_token( $code, $client_id, $client_secret, $use_auth_headers = FALSE ) {
            $params = array(
                'code' => $code,
                'client_id' => $client_id,
                'redirect_uri' => $this->callback_url,
                'grant_type' => 'authorization_code'
            );
            
            if( $use_auth_headers ){
                // Not all OAuth2.0 providers accepts Basic Authentication header
                $auth_header = 'Basic ' . $client_secret ;
                return $this->ci->curl->post( $this->url_access_token, $params, $auth_header);
            }else{
                $params['client_secret'] = $client_secret;
                return $this->ci->curl->post( $this->url_access_token, $params);
            }
        }
        
        /**
         * Make API calls
         * @param string $endpoint_uri
         * @param string $access_token
         * @param associative array $params
         * @param enum(get,post) $method
         * @param string token_type
         * @return plain get response body
         */
        function api($endpoint_uri, $access_token, $token_type = NULL, $params = array(), $method = 'get', $postbody = NULL) {
            
            $url = rtrim($this->url_api_base, '/') . '/' . trim($endpoint_uri, '/');
            
            if( !is_null($token_type) && preg_match('/bearer/i', $token_type)) {
                $auth_header = 'Bearer ' . $access_token;
            } else {
                $auth_header = 'OAuth ' . $access_token;
            }
            
            return $this->ci->curl->{$method}($url, $params, $auth_header);
        }

        /**
         * Refresh the access token (when expired)
         * @param refresh_token
         * @param client_id
         * @param client_secret
         */
        public function refresh_access_token($refresh_token, $client_id, $client_secret) {
            $params = array(
                'grant_type' => 'refresh_token',
                'client_id' => $client_id,
                'refresh_token' => $refresh_token
            );
            
            $auth_header = 'Basic ' . $client_secret ;
            
            $json = $this->ci->curl->post($url_access_token, $params, $auth_header);
            $data = json_decode($json);
            
            if (isset($data->error)) {
                $this->error = 'Did not receive refresh token';
                return FALSE;
            }
            return $data ;
        }

    }

    