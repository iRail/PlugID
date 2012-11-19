<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/API_Controller.php';

class Test extends API_Controller {
    
    function index() {
        if (!$this->is_authenticated()) {
            $this->output->set_output('you are not authenticated');
        } else {
            $this->output->set_output(json_encode('Yay, your first own api function!'));
        }
    }

}
    