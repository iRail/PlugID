<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
 
include 'application/core/api_controller.php' ;

class Test extends Api_Controller {
    
    function index(){
        if( !$this->is_authenticated() ){
            return false ;
        }else{
            $this->output->set_output( json_encode('Yay, your first own api function!') );
        }
    }
    
}
    