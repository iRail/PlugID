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

        $db->trips->update($where, $push);

        return array_merge($where, $data);
    }

    public function get($user_id, $id = null)
    {
        $db = $this->mongo();

        // using mapreduce (js functions)
        $map = new MongoCode("
            function() 
            {
                if (this.messages) 
                {
                    for (i in this.messages)
                    {
                        if (this.messages[i].user_id == '". $user_id ."' && ".
                            ($id ? "this.messages[i].message_id == '$id'" : 'true')
                           .")
                        {
                            o = this.messages[i];
                            
                            // add fields
                            o.tid = this.tid; o.sid = this.sid; o.date = this.date;

                            // emit
                            emit(this.messages[i].message_id, o);
                        }
                    }
                }
            }"
        );
        
        // simple
        $reduce = new MongoCode("
            function(k, val) 
            {
                // in case of non-unique message_ids
                return val instanceof Array ? val[0] : val;
            }"
        );

        $r = $db->command(array(
            "mapreduce" => "trips", 
            "map" => $map,
            "reduce" => $reduce,
            "out" => array("inline" => 1),
        ));

        // only the values, not the keys
        foreach ($r['results'] as &$message) 
        {
            $message = $message['value'];
        }

        return $r['results'];
    }

    public function update($data)
    {
        //$db = $this->mongo();
    }

    public function destroy($user_id, $id)
    {
        $db = $this->mongo();

        $old = $this->get($user_id, $id);
        if (empty($old))
        {
            return array('success' => false);
        }
        else
        {
            $old = reset($old);
        }

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

        $result = $db->trips->update(
            $where,
            array('$pull' => array('messages' => $comment))
        );

        if ($result['n'] == 1 && $old)
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
