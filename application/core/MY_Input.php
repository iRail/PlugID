<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Input extends CI_Input {
    
    /**
     * Turn xss_clean on by default
     */
    function post($index = NULL, $xss_clean = TRUE) {
        return parent::post($index, $xss_clean);
    }
    
}