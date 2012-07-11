<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Jens Segers <jens at iRail.be>
     * @author Hannes Van De Vreken <hannes at iRail.be>
     */
    class OAuth2_client {

        private $settings, $ci, $service;
        private $token = FALSE, $token_type = FALSE, $refresh_token = FALSE;
        private $hash_algo = 'md5';

        function __construct($config = array()) {
            $this->ci = &get_instance();
            $this->service = $config['service'];

            // get config
            $this->ci->config->load('oauth2/' . $this->service, TRUE);
            $this->settings = $this->ci->config->item('oauth2/' . $this->service);
        }

        public function set_authentication($config) {
            $this->token = $config->access_token;
            $this->refresh_token = $config->refresh_token;
            $this->token_type = $config->token_type;
        }

        /**
         * Redirect to authorize url
         * @param array $options
         */
        public function authorize($options = array()) {
            // build params
            $params = array(
                'client_id' => $this->settings['client_id'],
                'redirect_uri' => $this->settings['callback_url'],
                'response_type' => 'code'
            );

            if ($this->settings['scope']) {
                $params['scope'] = $this->settings['scope'];
            }

            //Merge params with options
            $params = array_merge($options, $params);
            $url = $this->settings['url_authorize'];
            return $url . (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
        }

        /**
         * Request access token from code
         * @param string $code
         * @param array $options
         */
        public function get_access_token($code) {
            $params = array(
                'code' => $code,
                'client_id' => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'redirect_uri' => $this->settings['callback_url'],
                'grant_type' => 'authorization_code'
            );
            $this->ci->load->library('curl');

            //Should be post as stated in http://tools.ietf.org/html/draft-ietf-oauth-v2-28#section-4.4.2
            $json = $this->ci->curl->post($this->settings['url_access_token'], $params);
            //facebook returns URL string instead of actual JSON.
            if (!strstr($json, '{')) {
                $keyValues = new stdClass();
                $parts = explode('&', $json);
                foreach ($parts as $currentPart) {
                    list($key, $value) = explode("=", $currentPart);
                    $keyValues->$key = $value;
                }

                $data = $keyValues;
            } else {
                $data = json_decode($json);
            }

            if (isset($data->error) || !isset($data->access_token)) {
                $this->error = 'Did not receive authentication token';
                return FALSE;
            }

            $this->token = $data->access_token;
            $this->refresh_token = isset($data->refresh_token) ? $data->refresh_token : FALSE;
            $this->token_type = isset($data->token_type) ? $data->token_type : FALSE;


            return $data;
        }

        /**
         * Make API calls
         * @param string $endpoint
         * @param associative array $params
         * @return plain json
         */
        function api($endpoint, $params = array(), $method = 'get') {
            $this->ci->load->library('curl');

            $url = rtrim($this->settings['url_api_base'], '/') . '/' . trim($endpoint, '/');

            if (preg_match('/bearer/i', $this->token_type)) {
                $auth_header = 'Bearer ' . $this->token;
            } else {
                $auth_header = 'OAuth ' . $this->token;
            }

            return $this->ci->curl->{$method}($url, $params, $auth_header);
        }

        /**
         * Refresh the access token (when expired)
         * @param refresh_token
         */
        public function refresh_access_token($refresh_token) {
            $params = array(
                'grant_type' => 'refresh_token',
                'client_id' => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'refresh_token' => $refresh_token,
            );
            $this->ci->load->library('curl');

            $json = $this->ci->curl->post($url_access_token, $params);
            $data = json_decode($json);

            if (isset($data->error)) {
                $this->error = 'Did not receive refresh token';
                return FALSE;
            }
            //To Do : save new token and refresh_token in DB and set it in this class.
            return array(
                'token' => $data->token,
                'token_type' => $data->token_type
            );
        }

    }

    