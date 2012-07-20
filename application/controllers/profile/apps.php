<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Koen De Groote <koen at iRail.be>
 * @author Jens Segers <jens at iRail.be>
 */

/** Gives an overview of all the connected apps of an user
 * Here, an user can see all the connected apps and revoke access
 */
class Apps extends MY_Controller {

    function __construct() {
        parent::__construct();
        if (!$this->session->user_id) {
            $this->session->redirect = 'profile/apps';
            redirect('authenticate');
        }
    }

    /** Show all the connected apps
     *
     */
    function index() {
        $this->load->model('user_model');
        $results = $this->user_model->authorized_clients($this->session->user_id);
        
        $this->load->view('header.tpl');
        $this->load->view('profile/apps', array('results' => $results));
        $this->load->view('footer.tpl');
    }

    /** Revoke access of a client
     * @param $client_id The access of this client will be revoked
     */
    function revoke($client_id) {
        $this->load->model('client_model');
        
        if(!$client = $this->client_model->get($client_id)) {
            show_error('We could not find this client in our database. Detail: client ID not found');
        }
        
        if ($this->input->post('client_id') && $this->input->post('client_id') == $client_id) {
            $this->load->model('user_model');
            $this->user_model->revoke($this->session->user_id, $client_id);
            
            redirect('profile/apps');
        } else {
            $this->load->view('header.tpl');
            $this->load->view('profile/revoke', $client);
            $this->load->view('footer.tpl');
        }
    }
}