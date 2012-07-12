<?php
/**
 * Implementing OAuth 2 for clients
 * Using cURL to make the requests
 * @author Lennart Martens <lennart at iRail.be>
 * 
 */
class OAuth2 {
    
    // private members
    private $client_id;
    private $client_secret;
    private $callback_url;
    private $last_response;
    
    /**
     * Construct with service specific credentials
     * @param string $client_id
     * @param string $client_secret
     * @param stirng $callback_url
     */
    public function __construct($client_id, $client_secret, $callback_url) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->callback_url = $callback;
    }
    
    /**
     * @param string $url
     * @param string $code
     * @param boolean $use_auth_headers
     * @return array or FALSE if failure. Still has to check on errorcodes.
     */
    public function getAccessToken($url, $code, $use_auth_headers = FALSE) {
        $params = array('code' => $code, 'client_id' => $this->client_id, 'redirect_uri' => $this->callback_url, 'grant_type' => 'authorization_code');
        
        if ($use_auth_headers) {
            // Not all OAuth2.0 providers accepts Basic Authentication header
            $auth_header = 'Basic ' . $this->$client_secret;
            $json = $this->curl_post($url, $params, $auth_header);
        } else {
            $params['client_secret'] = $this->$client_secret;
            $json = $this->curl_post($url, $params);
        }
        if (empty($json)) {
            return FALSE;
        }
        
        return json_decode($json);
    }
    
    /**
     * @return boolean
     */
    public function fetch($url, $params = array(), $method = 'get') {
        if (!is_null($token_type) && preg_match('/bearer/i', $token_type)) {
            $auth_header = 'Bearer ' . $access_token;
        } else {
            $auth_header = 'OAuth ' . $access_token;
        }
        
        $this->last_response = $this->makeRequest($url, $method, $params, $auth_header);
        
        if (empty($this->last_response)) {
            return FALSE;
        }
        
        return $this->last_response;
    }
    
    /**
     * @return array
     */
    public function refreshAccessToken($url, $refresh_token) {
        $params = array('grant_type' => 'refresh_token', 'client_id' => $this->$client_id, 'refresh_token' => $refresh_token);
        
        $auth_header = 'Basic ' . $this->$client_secret;
        
        $json = $this->makeRequest($url, $params, $auth_header);
        $data = json_decode($json);
        
        /*if (isset($data->error)) {
            $this->error = 'Did not receive refresh token';
            return FALSE;
        }*/
        return $data;
    }
    
    /**
     * @return string
     */
    public function getLastResponse() {
        return $this->last_response;
    }
    
    /**
     * CURL request
     * @param string $path
     * @param string $method
     * @param array $params
     * @param string $auth_header
     */
    private function makeRequest($path, $method = 'GET', $params = array(), $auth_header = FALSE) {
        $curl = curl_init($url);
        $params = http_build_query($params, NULL, '&');
        
        switch (strtoupper($method)) {
            case 'GET' :
                $path .= (strpos($path, '?') ? '&' : '?') . $params;
                curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                break;
            case 'POST' :
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_POST, TRUE);
                break;
            case 'PUT' :
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case 'DELETE' :
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        if (!is_null($auth_header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header));
        }
        
        $data = curl_exec($curl);
        
        // error
        if ($data === FALSE) {
            $errno = curl_errno($curl);
            $error = curl_error($curl);
            
            curl_close($curl);
            throw new Exception($error);
        }
        
        curl_close($curl);
        return $data;
    }
}