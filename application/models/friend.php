<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FRIEND MODEL
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Friend extends CI_Model {

    /**
     * Add a new friend.
     *
     * @access   public
     * @param    array
     */
    public function create($data)
    {
        $this->db->insert('friends', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Return friend data between two users.
     *
     * @access   public
     * @param    int
     * @param    int
     * @return   array
     */
    public function read($user_id, $friend_id)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('friends');
        $this->db->select('');
        $this->db->limit(1);
        $this->db->where('user_id', $user_id);
        $this->db->where('friend_id', $friend_id);
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Return list of friends of a user.
     *
     * @access   public
     * @param    int
     * @param    int
     * @return   array
     */
    public function user($user_id, $last_relationship_id = FALSE)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('friends');
        $this->db->select('');
        $this->db->limit(1);
        $this->db->where('user_id', $user_id);
        $this->db->where('friend_id', $friend_id);
        if ($last_relationship_id) {
            $this->db->where('relationship_id <', $last_relationship_id);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Update relationship between two users.
     *
     * @access   public
     * @param    int
     * @param    array
     */
    public function update($relationship_id, $data)
    {
        $this->db->where('relationship_id', $relationship_id);
        $this->db->update('friends', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Delete relationship between two users.
     *
     * @access   public
     * @param    int
     */
    public function delete($relationship_id)
    {
        $this->db->where('relationship_id', $relationship_id);
        $this->db->delete('friends');
    }

    // --------------------------------------------------------------------

    /**
     * Delete all friends of a user.
     *
     * @access   public
     * @param    int
     */
    public function delete_all($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->or_where('friend_id', $user_id);
        $this->db->delete('friends');
    }
}

/* End of file friend.php */
/* File location : ./application/models/friend.php */