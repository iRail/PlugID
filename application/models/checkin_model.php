<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/Mongo_Model.php';

class Checkin_model extends Mongo_Model {

    public function create($data)
    {
        // insert into System Of Record
        $this->db->insert('checkins', $data);
        $data['id'] = $this->db->insert_id();

        // insert into Mongo
        $this->mongo_store($data);

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

        return $this->db->get_where('checkins', $where)->result_array();
    }

    public function update($data)
    {
        // safety
        unset($data['client_id']);

        // perform
        $this->db->where('user_id', (integer)$data['user_id']);
        $this->db->where('id', (integer)$data['id']);
        $this->db->update('checkins', $data);

        // build where
        $where = array(
            'user_id' => (integer)$data['user_id'],
            'id' => (integer)$data['id'],
        );

        // return useful data
        return $this->db->get_where('checkins', $where)->row_array();
    }

    public function destroy($user_id, $id)
    {
        $where = array(
            'user_id' => (integer)$user_id,
            'id' => (integer)$id
        );

        $old = $this->db->get_where('checkins', $where)->row_array();
        $result = $this->db->delete('checkins', $where);

        if ($result && $old)
        {
            $this->mongo_destroy($old);

            return array(
                'success' => true,
                'deleted' => $old,
            );
        }
        return array(
            'success' => false,
        );
    }
    
    private function mongo_store($data)
    {
        // connection
        $db = $this->mongo();

        // query
        $filter = array('date'=> $data['date'], 'tid' => $data['tid']);
        $cursor = $db->trips->find($filter)->sort(array('sequence'=> 1));

        // prepare
        $departed = false;
        $arrived = false;

        $checkin = array(
            'user_id' => (integer)$data['user_id'],
            'checkin_id' => (integer)$data['id'],
        );

        // loop
        foreach ($cursor as $row)
        {
            if ($row['sid'] == $data['arr']) 
            {
                if (isset($row['arrive']) && is_array($row['arrive'])) 
                {
                    $row['arrive'][] = $checkin;
                }
                else
                {
                    $row['arrive'] = array($checkin);
                }
                $arrived = true;
            }

            if ($departed && !$arrived)
            {
                if (isset($row['on_vehicle']) && is_array($row['on_vehicle']))
                {
                    $row['on_vehicle'][] = $checkin;
                }
                else
                {
                    $row['on_vehicle'] = array($checkin);
                }
            }

            if ($row['sid'] == $data['dep'])
            {
                if (isset($row['depart']) && is_array($row['depart']))
                {
                    $row['depart'][] = $checkin;
                }
                else
                {
                    $row['depart'] = array($checkin);
                }
                $departed = true;
            }
            $db->trips->save($row);
        }
    }

    private function mongo_destroy($data)
    {
        // connection
        $db = $this->mongo();

        $checkin = array(
            'user_id' => (integer)$data['user_id'],
            'checkin_id' => (integer)$data['id'],
        );

        // unset
        foreach (array('depart', 'arrive', 'on_vehicle') as $key) 
        {
            $db->trips->update(
                array("$key.checkin_id" => (integer)$data['id']),
                array('$pull' => array($key => $checkin)),
                array('multiple' => true)
            );
        }
    }
}
