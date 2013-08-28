<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BAN MODEL
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Ban extends CI_Model {

    /**
     * Ban a user.
     *
     * @access   public
     * @param    array
     */
    public function create($user)
    {
        $this->db->insert('ban', $user);
    }

    // --------------------------------------------------------------------

    /**
     * Return data about banned user.
     *
     * @access   public
     * @param    int
     * @return   array
     */
    public function read($user_id)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('ban');
        $this->db->select('ban_id, user_id, offence, initiator, timestamp, expire');
        $this->db->limit(1);
        $this->db->order_by('ban_id', 'DESC');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();

        return $query->result_array();
    }
}

/* End of file ban.php */
/* File location : ./application/models/ban.php */