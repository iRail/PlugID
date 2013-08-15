<?php
/**
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Hannes Van De Vreken <hannes at iRail.be>
 */

include APPPATH . 'core/Mongo_Model.php';

class Comment_model extends Mongo_Model {

    public function create($data)
    {
        // insert into Mongo
        $db = $this->mongo();

        // add id
        $data['message_id'] = uniqid();

        // compile where
        $where = array(
            'date' => $data['date'],
            'tid' => $data['tid'],
            'sid' => $data['sid'],
        );

        // unset vals
        foreach (array('date', 'tid', 'sid') as $key) {
            unset($data[$key]);
        }

        // addition
        $push = array(
            '$push' => array(
                'messages' => $data,
            ),
        );

        // in trip data
        $db->trips->update($where, $push);

        // duplicate
        $copy = $data;
        $data = array_merge($where, $data);
        $data['_id'] = $data['message_id'];
        unset($data['message_id']);
        $db->comments->insert($data);

        // return
        return array_merge($where, $copy);
    }

    public function get($user_id, $id = null)
    {
        $db = $this->mongo();

        $where = array(
            'user_id' => $user_id
        );

        if ($id)
        {
            $where['_id'] = $id;
        }

        // db action
        $result = iterator_to_array($db->comments->find($where));

        // clean
        foreach ($result as $key => &$value) {
            $value['message_id'] = $value['_id'];
            unset($value['_id']);
        }

        // return
        return array_values($result);
    }

    public function update($data)
    {
        $db = $this->mongo();

        $where = array(
            '_id' => $data['id'],
            'user_id'    => $data['user_id'],
        );

        $set = array(
            '$set' => array(
                'message' => $data['message'],
            ),
        );

        $result = $db->comments->update($where, $set);

        if ($result['n'] == 0)
        {
            return array();
        }

        $where = array(
            'messages.message_id' => $data['id'],
            'messages.user_id'    => $data['user_id'],
        );

        $set = array(
            '$set' => array(
                'messages.$.message' => $data['message'],
            ),
        );

        $result = $db->trips->update($where, $set);
        
        return $this->get($data['user_id'], $data['id']);
    }

    public function destroy($user_id, $id)
    {
        $db = $this->mongo();

        $old = $this->get($user_id, $id);

        // remove from indexed table
        $where = array(
            '_id'     => $id,
            'user_id' => $user_id,
        );

        // remove from indexed
        $result = $db->comments->remove($where);

        // check
        if ($result['n'] != 1)
        {
            return array(
                'success' => false,
            );
        }

        // take first
        $old = reset($old);

        // make copy
        $comment = array();
        $where = array();
        foreach ($old as $key => $value) {
            if (in_array($key, array('tid', 'sid', 'date')))
            {
                $where[$key] = $value;
            }
            else
            {
                $comment[$key] = $value;
            }
        }

        // pull
        $db->trips->update(
            $where,
            array('$pull' => array('messages' => $comment))
        );

        return array(
            'success' => true,
            'deleted' => $old,
        );
    }
}
