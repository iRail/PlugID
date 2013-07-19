<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

class Notify extends CI_Controller {

    /**
     * POST notify
     */
    public function index() 
    {
        // arrange
        $secret = $this->input->get('secret');
        $this->config->load('custom');
        $expected = $this->config->item('push_secret');

        // assert
        if ($secret != $expected) 
        {
            $this->output->set_status_header(401);
            return false;
        }
        elseif ($this->input->server('REQUEST_METHOD') != 'POST')
        {
            $this->output->set_status_header(405);
            return false;
        }

        // only use json
        $this->output->set_content_type('application/json');

        // perform action
        $data_string = trim(file_get_contents('php://input'));
        $data = json_decode($data_string, true);

        /*
        | $data will be a ServiceStop object with the following fields:
        | - sid (optional)
        | - stop
        | - tid
        | - type (optional, may be empty)
        | - platform (optional)
        | - departure_time
        | - departure_delay
        | - arrival_time
        | - arrival_delay
        | - cancelled (optional)
        | - arrive [array] objects(user_id, checkin_id)
        | - depart [array] objects(user_id, checkin_id)
        | - on_vehicle (optional) (!don't warn these people)
        | (arrive & depart are optional, but at least one should be present)
        */

        // prepare curl options
        $curl_options = array(
            CURLOPT_SSL_VERIFYPEER => false, // allow https without ca certificatied private key
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
            ),
        );

        // load model
        $this->load->model('user_model');
        $this->load->library('ci_curl');

        // check arrive and depart
        foreach (array('arrive', 'depart') as $key) {
           
            if (isset($data[$key]))
            {
                foreach ($data[$key] as $checkin) 
                {
                    $clients = $this->user_model->authorized_clients($checkin['user_id']);

                    foreach ($clients as $client) 
                    {
                        if ($client->notify_uri)
                        {
                            // build url
                            $url = $client->notify_uri . '?secret=' . $client->notify_secret;

                            // perform
                            $result = $this->ci_curl->simple_post($url, array(), $curl_options);
                            $this->output->set_output($result); return;
                        }
                    }
                }
            }
        }
    }
}