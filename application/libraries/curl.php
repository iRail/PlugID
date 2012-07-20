<?php

class curl{
    
    public $error_code;
    public $error;
    
    /**
     * Basic curl GET wrapper with extra options set
     * @param string $url The URL to send to
     * @param array $params The URL parameters
     * @param string $auth_header Optional Authorization header.
     * @return function 
     * @see execute($curl) 
     */
    public function get($url, $params = array(), $auth_header = NULL) {
        // build query string
        if (is_array($params)) {
            $params = http_build_query($params, NULL, '&');
        }
        
        // add parameters to url
        $url = $url . ( strpos($url, '?') ? '&' : '?' ) . $params;
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        if( !is_null( $auth_header ) ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header ) );
        }
        
        return $this->execute($curl);
    }
    
    /**
     * Basic curl POST wrapper with extra options set
     * @param string $url The URL to send to
     * @param array $params The URL parameters
     * @param string $auth_header Optional Authorization header.
     * @return type 
     */
    public function post($url, $params = array(), $auth_header = NULL) {
        // build query string
        if (is_array($params)) {
            $params = http_build_query($params, NULL, '&');
        }
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        if( !is_null( $auth_header ) ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header ) );
        }
        
        return $this->execute($curl);
    }
    
    /**
     * Execute the curl which was built in the get/post function
     * @see get($url,$params,$auth_header)
     * @see post($url,$params,$auth_header)
     * @param object $curl The curl object which must be executed.
     * @return string Empty string is failed, JSON if succes 
     */
    private function execute($curl) {
        // execute
        $data = curl_exec($curl);
        
        // error
        if ($data === FALSE) {
            $errno = curl_errno($curl);
            $error = curl_error($curl);
            
            curl_close($curl);
            
            $this->error_code = $errno;
            $this->error = $error;
            return FALSE;
        }
        
        curl_close($curl);
        return $data;
    }
}

?>