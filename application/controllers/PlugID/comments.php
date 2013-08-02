<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/API_REST_Controller.php';

class Comments extends API_REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('comment_model');
	}

    /**
     * GET /comments/
     */
    protected function all() 
    {
        $this->output->set_content_type('application/json');
    	return $this->comment_model->get($this->auth->user_id);
    }

    /**
     * GET /comments/{id}
     */
    protected function show($id) 
    {
    	return $this->comment_model->get($this->auth->user_id, $id);
    }

    /**
     * POST
     */
    protected function create($data) 
    {
        if ( ! isset($data['date'], $data['tid'], $data['sid'], $data['message']))
        {
            return array('success' => false, 'message' => 'required fields: date, tid, sid and message');
        }

    	// add data
    	$data['client_id'] = $this->auth->client_id;
    	$data['user_id']   = $this->auth->user_id;

        // add timestamp
        $data['created_at'] = date('c');

    	// perform insert
    	return $this->comment_model->create($data);
    }

    /**
     * PUT
     */
    protected function update($id, $data) 
    {
    	$data['user_id'] = $this->auth->user_id;
        $data['id'] = $id;
    	return $this->comment_model->update($data);
    }

    /**
     * DELETE
     */
    protected function destroy($id) 
    {
    	return $this->comment_model->destroy($this->auth->user_id, $id);
    }
}