<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * USER MODEL
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class User extends CI_Model {

    /**
     * Create user.
     *
     * @access   public
     * @param    array
     */
    public function create($user)
    {
        $this->db->insert('users', $user);
    }

    // --------------------------------------------------------------------

    /**
     * Return user data.
     *
     * @access   public
     * @param    mixed
     * @return   array
     */
    public function read($identifier)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('users');
        $this->db->select();
        $this->db->limit(1);
        $this->db->where('user_id', $identifier);
        $this->db->or_where('username', $identifier);
        $this->db->or_where('email', $identifier);
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Update user data.
     *
     * @access   public
     * @param    int
     * @param    array
     */
    public function update($user_id, $data)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('users', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Delete user.
     *
     * @access   public
     * @param    int
     */
    public function delete($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('users');
    }

    // --------------------------------------------------------------------

    /**
     * Search users.
     *
     * @access   public
     * @param    string
     * @param    int
     * @return   array
     */
    public function search($name, $last_user_id = FALSE)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('users');
        $this->db->like('name', $name);
        $this->db->limit(15);
        $this->db->order_by('user_id', 'desc');
        $this->db->where('type !=', 'deleted');
        if ($last_user_id) {
            $this->db->where('user_id <', $last_user_id);
        }
        $query = $this->db->get();

        return $query->result_array();
    }
}

/* End of file user.php */
/* File location : ./application/models/user.php */