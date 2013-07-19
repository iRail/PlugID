<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
class Mongo_model extends CI_Model {

	protected $mongo;
	private $monog_config;

	public function __construct()
	{
		$this->config->load('mongo');
        $this->mongo_config = $this->config;
	}

	protected function mongo()
	{
		if ( ! $this->mongo) {

            // build moniker
            $hosts = implode(',',$this->mongo_config->item('db_hosts'));
            $credentials = $this->mongo_config->item('db_username') . ':' . $this->mongo_config->item('db_passwd');
            $db = $this->mongo_config->item('db_name');

            // moniker
            $moniker = "mongodb://$credentials@$hosts/$db";
     		
            // start connection
            $this->mongo = new \MongoClient($moniker, array( 'connect' => false ));
        }

        return $this->mongo->{$this->mongo_config->item('db_name')};
	}
}