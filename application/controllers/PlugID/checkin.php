<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers <jens at iRail.be>
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/API_Controller.php';

class Checkin extends API_Controller {
    
    function index() {
        if (false){//!$this->is_authenticated()) {
            $this->output->set_output('you are not authenticated');
        } else {
        	$this->config->load('mongo');
        	$config = $this->config;

        	$hosts = implode(',',$config->item('db_hosts'));
			$moniker = "mongodb://" . $config->item('db_username') . ":" . $config->item('db_passwd') . "@$hosts/" . $config->item('db_name');

			$m = new \Mongo($moniker);
			$db = $m->{$config->item('db_name')};

			$data = new stdClass();
			$data->asc  = $this->input->post('asc');
			$data->desc = $this->input->post('desc');
			$data->tid  = $this->input->post('tid');
			$data->date = $this->input->post('date');
			$data->user_id = $this->input->post('user_id');

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
        }
    }

}