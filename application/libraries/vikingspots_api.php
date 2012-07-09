<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Koen De Groote <koen at iRail.be>
     */
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');
    require 'OAuth2_client';

    class Vikingspots_API {

        private $settings, $oa2c, $token = FALSE;
        // contains the last error
        public $error = FALSE;

        function __construct() {
            //Delegate get config to OAuth2_Client
            $this->oa2c = new OAuth2_client('vikingspots');
        }
        
        /**
         * Authorization url
         * @param string $callback
         * @return string The url we need to use to get an auth_token
         */
        function auth_url($callback = FALSE) {
            return $this->oa2c->authorize();
            //Sample return in $_GET: Array ( [code] => dd4c7b96e3ebdaaa613c430be49126 ) 
        }

        /**
         * Get OAuth token
         * @param string $code
         * @return string 
         */
        function request_token($code) {
            $json = $this->oa2c->get_access_token($code);
            return $json->access_token;
        }

        /**
         * Vikingspots API checkin info request method
         * @param string $id
         * @return String $json
         */
        function get_checkin($id) {
            $uri = 'checkins';
            $data = array();
            $data['checkin_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API coupon info request method
         * @param string $id
         * @return String $json
         */
        function get_coupon($id) {
            $uri = 'coupons';
            $data = array();
            $data['coupon_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API deal info request method
         * @param string $id
         * @return String $json
         */
        function get_deal($id) {
            $uri = 'coupons';
            $data = array();
            $data['deal_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API news info request method
         * @param string $id
         * @return String $json
         */
        function get_news($id) {
            $uri = 'coupons';
            $data = array();
            $data['news_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API notification info request method
         * @param string $id
         * @return String $json
         */
        function get_notification($id) {
            $uri = 'coupons';
            $data = array();
            $data['notification_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API spot info request method
         * @param string $id
         * @return String $json
         */
        function get_spot($id) {
            $uri = 'coupons';
            $data = array();
            $data['spot_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API user info request method
         * @param string $id
         * @return String $json
         */
        function get_user($id) {
            $uri = 'users';
            $data = array();
            $data['user_id'] = $id;

            $json = $this->oa2c->api($uri, 'GET', null, $data);
            return $json;
        }

        /**
         * Vikingspots API request method
         * @param string $uri
         * @param array $data
         * @return Object
         */
        function api($uri, $data = array(), $method = 'GET') {
            // url parameters
            $params = array();

            // active token set?
            if (!$token = $this->oa2c->token()) {
                // Userless acces on vikingspots API not yet explained.(07/Jul/2012)
                $params['client_id'] = $this->oa2c->settings['client_id'];
                $params['client_secret'] = $this->oa2c->settings['client_secret'];
            } else {
                $params['bearer_token'] = $token;
            }

            if (strtoupper($method) == 'POST') {
                $json = $this->oa2c->_post($this->oa2c->settings['url_api_base'] . $uri . '?' . http_build_query($params), $data);
            } else {
                $json = $this->oa2c->_get($this->oa2c->settings['url_api_base'] . $uri . '?' . http_build_query(array_merge($params, $data)));
            }

            if (!$json) {
                $this->oa2c->error = 'No response from Vikingspots API';
                return FALSE;
            } elseif ($json->meta->code != 200) {
                $this->oa2c->error = $json->meta->errorDetail;
                return FALSE;
            }

            return $json;
        }

        /**
         * Raw CURL get method
         * @param string $url
         * @return Object
         */
        private function _get($url) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

            $data = curl_exec($curl);
            curl_close($curl);

            if (!$data) {
                return FALSE;
            }

            return json_decode($data);
        }

        /**
         * Raw CURL post method
         * @param string $url
         * @param array $data 
         * @return Object
         */
        private function _post($url, $data) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

            $data = curl_exec($curl);
            curl_close($curl);

            if (!$data) {
                return FALSE;
            }

            return json_decode($data);
        }

    }

?>