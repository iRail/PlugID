<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/API_REST_Controller.php';

class Checkins extends API_REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('checkin_model');
	}

    /**
     * GET /checkins/
     */
    protected function all() 
    {
    	return $this->checkin_model->get($this->auth->user_id);
    }

    /**
     * GET /checkins/{id}
     */
    protected function show($id) 
    {
    	return $this->checkin_model->get($this->auth->user_id, $id);
    }

    /**
     * POST
     */
    protected function create($data) 
    {
        // input check
        if ( ! isset($data['dep'], $data['arr'], $data['tid'], $data['date']))
        {
            return array('success' => false, 'message' => 'required fields: dep, arr, tid, date');
        }

    	// add data
    	$data['client_id'] = $this->auth->client_id;
    	$data['user_id']   = $this->auth->user_id;
        
        // add timestamp
        $data['created_at'] = date('c');

    	// perform insert
    	return $this->checkin_model->create($data);
    }

    /**
     * PUT
     */
    protected function update($id, $data) 
    {
        // just for the purpose of showing how it's done:
        /*
    	$data['user_id'] = $this->auth->user_id;
    	$data['id'] = $id;
    	return $this->checkin_model->update($data);
        */
    }

    /**
     * DELETE
     */
    protected function destroy($id) 
    {
    	return $this->checkin_model->destroy($this->auth->user_id, $id);
    }
}