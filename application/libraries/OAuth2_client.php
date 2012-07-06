<?php

class OAuth2_client extends client {
    
    public $url_authorize = '';
    public $url_access_token = '';
    public $callback;
    public $client_id = '';
    public $client_secret = '';
    
    /**
     * Redirect to authorize url
     * @param array $options
     */
    public function authorize($options = array()) {
        // build params
        $params = array();
        $params['client_id'] = $this->client_id;
        $params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->redirect_uri;
        $params['state'] = md5(uniqid(rand(), TRUE));
        $params['response_type'] = 'code';
        
        if ($options['scope']) {
            $params['scope'] = $options['scope'];
        }
        
        // redirect
        redirect($this->url_authorize . '?' . http_build_query($params));
    }
    
    /**
     * Request access token from code
     * @param string $code
     * @param array $options
     */
    public function access_token($code, $options = array()) {
        $params = array();
        $params['code'] = $code;
        $params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->redirect_uri;
        $params['grant_type'] = 'authorization_code';
        $params['client_id'] = $this->client_id;
        $params['client_secret'] = $this->client_secret;
        
        $ci = &get_instance();
        $ci->load->library('curl');
        
        $data = $ci->curl->post($path, $params);
        $json = json_decode($data);
        
        // response
        $token = $json->access_token;
        $expires = $json->expires_in ? $json->expires_in : FALSE;
        $refresh_token = $json->refresh_token ? $json->refresh_token : FALSE;
        $scope = $json->scope ? $json->scope : FALSE;
    }
    
    /**
     * Make API calls
     * @param string $path
     * @param string $method
     * @param array $params
     */
    public function api($path, $method = 'GET', $params = array()) {
        $ci = &get_instance();
        $ci->load->library('curl');
        
        $method = strtolower($method);
        
        $data = $ci->curl->$method($path, $params);
        return json_decode($data);
    }

}
