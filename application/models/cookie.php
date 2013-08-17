<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * COOKIE MODEL
 *
 * @package    FCstuff
 * @category   Authentication
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Cookie extends CI_Model {

    /**
     * Add a new authentication token.
     *
     * @access   public
     * @param    array
     */
    public function create($cookie)
    {
        $this->db->insert('cookies', $cookie);
    }

    // --------------------------------------------------------------------

    /**
     * Return details about an authentication token.
     *
     * @access   public
     * @param    int
     * @param    int
     * @return   array
     */
    public function read($user_id, $token)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('cookies');
        $this->db->select('');
        $this->db->limit(1);
        $this->db->where('user_id', $user_id);
        $this->db->where('token', $token);
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Update an existing authentication token.
     *
     * @access   public
     * @param    int
     * @param    array
     */
    public function update($cookie_id, $data)
    {
        $this->db->where('cookie_id', $cookie_id);
        $this->db->limit(1);
        $this->db->update('cookies', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Delete an authentication token.
     *
     * @access   public
     * @param    int
     */
    public function delete($cookie_id)
    {
        $this->db->where('cookie_id', $cookie_id);
        $this->db->delete('cookies');
    }
}

/* End of file cookie.php */
/* File location : ./application/models/cookie.php */