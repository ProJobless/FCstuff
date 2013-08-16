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
     * @param    string
     * @param    int
     * @param    int
     * @return   array
     */
    public function read($content_type, $content_id, $last_rating_id = FALSE)
    {
        $this->db->from('ratings');
        $this->db->select('');
        $this->db->where('type', $content_type);
        $this->db->where('content_id', $content_id);
        if ($last_rating_id) {
            $this->db->where('rating_id <', $last_rating_id);
        }
        $this->db->limit(15);
        $this->db->order_by('rating_id', 'desc');
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