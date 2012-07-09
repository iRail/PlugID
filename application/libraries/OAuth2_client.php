<?php

    class OAuth2_client extends client {

        private $settings, $ci, $service, $token = FALSE;

        function __construct($service) {
            $this->ci = &get_instance();

            // get config
            $this->ci->config->load('oauth2/' . strtolower($service), TRUE);
            $this->service = $service;
            $this->settings = $this->ci->config->item(strtolower($service));
        }

        /**
         * Set the token to use for following request
         */
        function token() {
            return $this->token;
        }

        /**
         * Get the current token
         * @param string $token
         */
        function set_token($token) {
            return $this->token = $token;
        }

        /**
         * Set the refresh token to use for following request
         */
        function refresh_token() {
            return $this->refresh_token;
        }

        /**
         * Get the current refresh token
         * @param string $refreshtoken
         */
        function set_refresh_token($refresh_token) {
            return $this->refresh_token = $refresh_token;
        }

        /**
         * Redirect to authorize url
         * @param array $options
         */
        public function authorize($options = array()) {
            // build params
            $params = array();
            $params['client_id'] = $this->settings['client_id'];
            $params['redirect_uri'] = $this->settings['callback_url'];

            //Prevents CSRF
            $params['state'] = md5(uniqid(rand(), TRUE));
            $params['response_type'] = 'code';

            if ($this->settings['scope']) {
                $params['scope'] = $this->settings['scope'];
            }

            //Merge params with options
            array_merge($options, $params);
            $url = $this->settings['url_authorize'];
            return $url . (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
        }

        /**
         * Request access token from code
         * @param string $code
         * @param array $options
         */
        public function get_access_token($code) {
            $params = array();
            $params['code'] = $code;
            $params['redirect_uri'] = $this->settings['callback_url'];
            $params['grant_type'] = 'authorization_code';
            $params['client_id'] = $this->settings['client_id'];
            $params['client_secret'] = $this->settings['client_secret'];

            if ($this->settings['scope']) {
                $params['scope'] = $this->settings['scope'];
            }
            $this->ci->load->library('curl');

            //Post as stated in http://tools.ietf.org/html/draft-ietf-oauth-v2-28#section-4.4.2
            $data = $this->ci->curl->post($this->settings['url_access_token'], $params);
            $json = json_decode($data);


            if (isset($json->error) || !isset($json->access_token)) {
                $this->error = 'Did not receive authentication token';
                return FALSE;
            }

            $this->set_token($json->access_token);

            // response
            $token = $json->access_token;
            $token_type = $json->token_type ? $json->token_type : FALSE;
            $refresh_token = $json->refresh_token ? $json->refresh_token : FALSE;

            return array($token, $refresh_token, $token_type);
        }

        /**
         * Make API calls
         * @param string $path
         * @param string $method
         * @param array $params
         */
        public function api($uri, $method = 'GET', $postBody = null, $uriParameters = array()) {
            $this->ci->load->library('curl');
            $parameters = null;

            if (strtoupper($method) !== 'GET') {
                if (is_array($postBody)) {
                    $postBody['oauth_token'] = $this->token;
                    $parameters = http_build_query($postBody);
                } else {
                    $postBody .= '&oauth_token=' . urlencode($this->token);
                    $parameters = $postBody;
                }
            } else {
                $uriParameters['oauth_token'] = $this->token;
            }

            $url = $this->settings['url_api_base'] . $uri;
            if (!empty($uriParameters)) {
                $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($uriParameters);
            }

            $method = strtolower($method);
            //Variable Variables: http://php.net/manual/en/language.variables.variable.php
            $json = $this->ci->curl->{$method}($url, $parameters);

            $data = json_decode($json);
            if (!$json || isset($data->error)) {
                $this->error = 'No response from ' . $service . ' API';
                return FALSE;
            }
            return $json;
        }

        public function refresh_access_token($refresh_token) {
            $params = array(
                'grant_type' => 'refresh_token',
                'client_id' => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'refresh_token' => $refresh_token,
            );
            $this->ci = &get_instance();
            $this->ci->load->library('curl');

            $data = $this->ci->curl->post($url_access_token, $params);
            $json = json_decode($data);

            if (isset($json->error)) {
                return FALSE;
            }
            //To Do : save new token and refresh_token in DB and set it in this class.
        }

    }

    