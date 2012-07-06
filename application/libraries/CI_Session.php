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
        
        // start up session
        session_start();
    }
    
    function set($name, $value){
        $_SESSION[$name] = $value ;
    }
    
    function get($name){
        if( isset($_SESSION[$name]) ){
            return $_SESSION[$name];
        }else{
            
        }
    }
    
    function remove($name){
        
    }
}
    