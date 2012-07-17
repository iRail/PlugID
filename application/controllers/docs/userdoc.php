<?php

    /**
     * @copyright (C) 2012 by iRail vzw/asbl
     * @license AGPLv3
     * @author Koen De Groote <koen at iRail.be>
     */
    class Userdoc extends CI_Controller {

        private $data = array();

        function index() {
            $this->load->view('header.tpl');
            $this->load->view('docs/userdoc');
            $this->load->view('footer.tpl');
        }

    }

?>