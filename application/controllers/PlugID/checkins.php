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
     * GET /checkins/1
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
    	// add data
    	$data['client_id'] = $this->auth->client_id;
    	$data['user_id']   = $this->auth->user_id;

    	// perform insert
    	return $this->checkin_model->create($data);
    }

    /**
     * PUT
     */
    protected function update($id, $data) 
    {
    	$data['user_id'] = $this->auth->user_id;
    	$data['id'] = $id;
    	return $this->checkin_model->update($data);
    }

    /**
     * DELETE
     */
    protected function destroy($id) 
    {
    	return $this->checkin_model->destroy($this->auth->user_id, $id);
    }
}

/*
			$filter = array('date'=> $data->date, 'tid' => $data->tid);

			$cursor = $db->trips->find(array('date'=> $data->date, 'tid' => $data->tid))->sort(array('sequence'=> 1));

			$ascended = false;
			$descended = false;

			foreach ($cursor as $row) {

				if ($row['sid'] == $data->desc) {
					if (is_array($row['dismounting'])) {
					
						$row['dismounting']["$data->user_id"] = 1;
					
					}else{

						$row['dismounting'] = array("$data->user_id" => 1);
					}
					$descended = true;
				}

				if ($ascended && !$descended) {
					if (is_array($row['sitting'])) {
					
						$row['sitting']["$data->user_id"] = 1;
					
					}else{

						$row['sitting'] = array("$data->user_id" => 1);
					}
				}

				if ($row['sid'] == $data->asc) {
					if (is_array($row['boarding'])) {
					
						$row['boarding']["$data->user_id"] = 1;
					
					}else{

						$row['boarding'] = array("$data->user_id" => 1);
					}
					$ascended = true;
				}

				$db->trips->save($row);
			}

			$this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(array('success' => true)));
            */