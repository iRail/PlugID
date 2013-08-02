<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/API_Controller.php';

class User extends API_Controller {
    
    function index() {
    	$token = $this->is_authenticated();
        if (!$token) 
        {
            $this->output->set_output('you are not authenticated');
        } 
        else 
        {
        	$this->load->model('access_token_model');
            $this->load->model('user_model');
            $this->load->driver('service');

        	$user = $this->access_token_model->get_user($token);

            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(compact('user')));
        }
    }

}
    