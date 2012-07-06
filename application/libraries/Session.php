<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_Session{
    
    // expire time for cookie (31 days)
    private $expire = 2678400;
    private $ci;
    
    function __construct(){
        $this->ci = &get_instance();
        $this->ci->load->library('encrypt');
        // start up session
        session_start();
    }
    
    function __set($name, $val){
        $_SESSION[$name] = $val ;
        return $this ;
    }
    
    function __get($name){
        if( isset($_SESSION[$name]) ){
            return $_SESSION[$name];
        }
        return false ;
    }
    
    function __isset($name){
        return isset($_SESSION[$name]);
    }
    
    function __unset($name){
        unset($_SESSION[$name]);
        return $this;
    }
}
    