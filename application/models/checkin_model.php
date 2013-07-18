<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */
class Checkin_model extends CI_Model {

    private $mongo;

    private function mongo()
    {
        if ( ! $this->mongo) {
            // get config
            $this->config->load('mongo');
            $config = $this->config;

            // build moniker
            $hosts = implode(',',$config->item('db_hosts'));
            $moniker = "mongodb://" . $config->item('db_username') . ":" . $config->item('db_passwd') . "@$hosts/" . $config->item('db_name');
        
            // start connection
            $m = new \Mongo($moniker);
            $this->mongo = $m->{$config->item('db_name')};
        }

        return $this->mongo;
    }

    public function create($data)
    {
        $this->db->insert('checkins', $data);
        $data['id'] = $this->db->insert_id();

        $db = $this->mongo();

        return $data;
    }

    public function get($user_id, $id = null)
    {
        $where = array(
            'user_id' => $user_id
        );

        if ($id)
        {
            $where['id'] = $id;
        }

        return $this->db->get_where('checkins', $where)->result();
    }

    public function update($data)
    {
        // safety
        unset($data['client_id']);

        // perform
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('id', $data['id']);
        $this->db->update('checkins', $data);

        // build where
        $where = array(
            'user_id' => $data['user_id'],
            'id' => $data['id'],
        );

        // return useful data
        return $this->db->get_where('checkins', $where)->result();
    }

    public function destroy($user_id, $id)
    {
        $where = array(
            'user_id' => $user_id,
            'id' => $id
        );

        $old = $this->db->get_where('checkins', $where)->result(); 
        $result = $this->db->delete('checkins', $where);

        if ($result && $old)
        {
            return array(
                'success' => true,
                'deleted' => $old,
            );
        }
        return array(
            'success' => false,
        );
    }
    
}
