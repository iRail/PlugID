<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_Session {
    
    private $ci;
    
    function __construct() {
        $this->ci = &get_instance();
        
        // start up session
        //ini_set('session.cookie_domain', base_url());
        session_name('plugid');
        session_start();
    }
    
    function __set($name, $val) {
        $_SESSION[$name] = $val;
        return TRUE;
    }
    
    function __get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return FALSE;
    }
    
    function __isset($name) {
        return isset($_SESSION[$name]);
    }
    
    function __unset($name) {
        unset($_SESSION[$name]);
        return $this;
    }
}
    