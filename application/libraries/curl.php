<?php

class curl{
    
    public $error_code;
    public $error;
    
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