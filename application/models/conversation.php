<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CONVERSATION MODEL
 *
 * @package    FCstuff
 * @category   Conversation
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Conversation extends CI_Model {

    /**
     * Add a new message.
     *
     * @access   public
     * @param    array
     */
    public function create($message)
    {
        $this->db->insert('conversations', $message);
    }

    // --------------------------------------------------------------------

    /**
     * Return conversation between two users.
     *
     * @access   public
     * @param    int
     * @param    int
     */
    public function read($user_id, $friend_id, $before = FALSE, $after = FALSE)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('conversations');
        $this->db->select('message_id, user_id, friend_id, type, message, timestamp, seen');
        $this->db->order_by('message_id', 'desc');
        $this->db->limit(15);
        $this->db->where('user_id', $user_id);
        $this->db->where('friend_id', $friend_id);
        if ($before) {
            $this->db->where('message_id <', $before);
        }
        elseif ($after) {
            $this->db->where('message_id >', $after);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Delete all conversations of a user.
     *
     * @access   public
     * @param    int
     */
    public function delete_all($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('conversations');
    }
}

/* End of file conversation.php */
/* File location : ./application/models/conversation.php */