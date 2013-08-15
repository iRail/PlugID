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
        // create ID
        $data['id'] = uniqid();

        // insert into System Of Record
        $result = $this->mongo_store($data);

        // check if input was valid
        if (! $result)
        {
            return array(
                'success' => $result
            );
        }

        // insert into indexing system
        $this->db->insert('checkins', $data);

        // return in the end
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
        $this->db->where('id', $data['id']);
        $this->db->update('checkins', $data);

        // build where
        $where = array(
            'user_id' => (integer)$data['user_id'],
            'id' => $data['id'],
        );

        // return useful data
        return $this->db->get_where('checkins', $where)->row_array();
    }

    public function destroy($user_id, $id)
    {
        $where = array(
            'user_id' => (integer)$user_id,
            'id' => $id
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
            'checkin_id' => $data['id'],
        );

        $save_later = array();

        // loop
        foreach ($cursor as $row)
        {
            // shortcut
            if ($departed && $arrived) break;

            if ($departed && ! $arrived &&
                $row['sid'] == $data['arr']) 
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

            if ($departed && ! $arrived)
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

            $save_later[] = $row;
        }

        // check outcome
        if ($departed && $arrived)
        {
            // it's ok to save them
            foreach ($save_later as $row) 
            {
                $db->trips->save($row);
            }
            // it's a okay
            return true;
        }

        // not the rights 'arr' and 'dep' fields after all.
        return false;
    }

    private function mongo_destroy($data)
    {
        // connection
        $db = $this->mongo();

        $checkin = array(
            'user_id' => (integer)$data['user_id'],
            'checkin_id' => $data['id'],
        );

        $or = array();
        $update = array();

        // all fields
        foreach (array('depart', 'arrive', 'on_vehicle') as $key) 
        {
            $or[] = array("$key.checkin_id" => $data['id']);
            $update[$key] = $checkin;
        }

        // update
        $db->trips->update(
            array('$or' => $or),
            array('$pull' => $update),
            array('multiple' => true)
        );
    }
}
