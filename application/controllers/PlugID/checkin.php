<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/API_REST_Controller.php';

class Checkin extends API_REST_Controller {

    /**
     *
     */
    protected function all() {
    	echo "all";
    }

    /**
     *
     */
    protected function show($id) {
    	echo "show($id)";
    }

    /**
     * POST
     */
    protected function create($data) {
    	echo "create";

    	var_dump($data);
    }

    /**
     * PUT
     */
    protected function update($id, $data) {
    	echo "update $id";
    }

    /**
     * DELETE
     */
    protected function destroy($id) {
    	echo "destroy $id";
    }
}