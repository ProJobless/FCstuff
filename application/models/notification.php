<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NOTIFICATION MODEL
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Notification extends CI_Model {

    /**
     * Create a new notification.
     *
     * @access   public
     * @param    array
     */
    public function create($notification)
    {
        $this->db->insert('notifications', $notification);
    }

    // --------------------------------------------------------------------

    /**
     * Return data about a notification.
     *
     * @access   public
     * @param    int
     * @return   array
     */
    public function read($id)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('notifications');
        $this->db->select('notification_id, user_id, content, image, link, timestamp, seen');
        $this->db->where('notification_id', $id);
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Return notifications for a user.
     *
     * @access   public
     * @param    int
     * @param    int
     * @param    int
     * @return   array
     */
    public function user($user_id, $before = FALSE, $after = FALSE)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('notifications');
        $this->db->select('notification_id, user_id, content, image, link, timestamp, seen');
        $this->db->where('user_id', $user_id);
        if ($before) {
            $this->db->order_by('notification_id', 'desc');
            $this->db->where('notification_id <', $before);
            $this->db->limit(15);
        }
        elseif ($after) {
            $this->db->order_by('notification_id', 'asc');
            $this->db->where('notification_id >', $after);
            $this->db->limit(1);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Delete notification.
     *
     * @access   public
     * @param    int
     */
    public function delete($notification_id)
    {
        $this->db->where('notification_id', $notification_id);
        $this->db->delete('notifications');
    }

    // --------------------------------------------------------------------

    /**
     * Delete all notification for a user.
     *
     * @access   public
     * @param    int
     */
    public function delete_all($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('notifications');
    }
}

/* End of file notification.php */
/* File location : ./application/models/notification.php */