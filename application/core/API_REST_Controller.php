<?php

/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 *
 * How to extend this controller:
 * 
 * Add a directive to your routes.php config file:
 * 
 * $route['path/to/resource/(:any)'] = 'path/to/resource/index/$1';
 *
 * with 'resource' equal to the class name.
 * implement the functions 
 *     all(), 
 *     show($id), 
 *     create($data), 
 *     update($id, $data), 
 *     destroy($id)
 *
 * use JSON to post/put to your new RESTful controllers.
 */

include APPPATH . 'core/API_Controller.php';

class API_REST_Controller extends API_Controller{
    
    public function index($id = false) 
    {
        if ( ! $this->is_authenticated()) 
        {
            $this->output->set_status_header(401);
            $this->output->set_output('you are not authenticated');
        }
        else 
        {
            // if it has data in body, get it!
            $data = json_decode(trim(file_get_contents('php://input')), true);

            // decide what REST function to use
            if ($this->input->server('REQUEST_METHOD') == 'GET')
            {
                if ( ! $id)
                {
                    $this->json($this->all());
                } else {
                    $this->json($this->show($id));
                }
            }
            elseif ($this->input->server('REQUEST_METHOD') == 'POST')
            {
                if ($id)
                {
                    $this->output->set_status_header(405);
                    return false;
                }

                if ( ! $data)
                {
                    $this->json(array('success' => false, 'message' => 'invalid json input'));
                }
                else
                {
                    $this->json($this->create($data));
                }
            }
            elseif (in_array($this->input->server('REQUEST_METHOD'), array('PUT', 'PATCH')) )
            {
                if ( ! $id)
                {
                    $this->output->set_status_header(405);
                    return false;
                }

                if ( ! $data)
                {
                    $this->json(array('success' => false, 'message' => 'invalid json input'));
                }
                else
                {
                    $this->json($this->update($id, $data));
                }
            }
            elseif ($this->input->server('REQUEST_METHOD') == 'DELETE')
            {
                if ( ! $id)
                {
                    $this->output->set_status_header(405);
                    return false;
                }

                $this->json($this->destroy($id));
            }
            else 
            {
                $this->output->set_status_header(405);
                return false;
            }
        }
    }

    private function json($data)
    {
        $this->output->set_status_header(empty($data) ? 404: 200);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
}
