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
        if (!$token) {
            $this->output->set_output('you are not authenticated');
        } else {
        	$this->load->model('access_token_model');
            $this->load->model('user_model');
            $this->load->driver('service');

        	$user = $this->access_token_model->get_user($token);

            $tokens = $this->user_model->get_tokens($this->auth->user_id, 'foursquare');
            $token = reset($tokens);

            $this->service->foursquare->set_authentication($token);
            $result    = json_decode($this->service->foursquare->api('users/self/checkins?limit=1'));
            $user_data = json_decode($this->service->foursquare->api('users/self'));

            if (isset($result->response->checkins->items) &&
                count($result->response->checkins->items) > 0 &&
                isset($user_data->response->user))
            {
                // subdoc
                $user_data = $user_data->response->user;

                $checkin = $result->response->checkins->items[0];
                $location = $checkin->venue->location;

                //unset($location->postalCode);
                //unset($location->city);
                unset($location->country);
                unset($location->cc);
                //unset($location->address);

                $user->location = $location;
                $user->first_name = $user_data->firstName;
                $user->last_name = $user_data->lastName;
            }

            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(compact('user')));
        }
    }

}
    