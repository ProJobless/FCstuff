<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RATING MODEL
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Rating extends CI_Model {

    /**
     * Add a new rating.
     *
     * @access   public
     * @param    array
     */
    public function create($rating)
    {
        $this->db->insert('ratings', $rating);
    }

    // --------------------------------------------------------------------

    /**
     * Return ratings for a post / comment.
     *
     * @access   public
     * @param    int
     * @param    int
     * @return   array
     */
    public function read($post_id, $user_id)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('ratings');
        $this->db->select('rating_id, post_id, user_id, rating, timestamp');
        $this->db->where('post_id', $post_id);
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Update an existing rating.
     *
     * @access   public
     * @param    int
     * @param    array
     */
    public function update($rating_id, $rating)
    {
        $this->db->where('rating_id', $rating_id);
        $this->db->update('ratings', $rating);
    }
}

/* End of file rating.php */
/* File location : ./application/models/rating.php */