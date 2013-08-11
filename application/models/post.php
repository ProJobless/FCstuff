<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POST MODEL
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Post extends CI_Model {

    /**
     * Create a new post.
     *
     * @access   public
     * @param    array
     */
    public function create($post)
    {
        $this->db->insert('posts', $post);
    }

    // --------------------------------------------------------------------

    /**
     * Return contents of a single post.
     *
     * @access   public
     * @param    int
     * @return   array
     */
    public function read($post_id)
    {
        $this->db->from('posts');
        $this->db->select('');
        $this->db->limit(1);
        $this->db->where('post_id', $post_id);
        $query = $this->db->get();

        return $query->result_array();
    }

   // --------------------------------------------------------------------

    /**
     * Return posts made by an user.
     *
     * @access   public
     * @param    int
     * @param    int
     * @param    int
     * @return   array
     */
    public function user($user_id, $limit = 5, $offset = 0)
    {
        $this->db->from('posts');
        $this->db->select('');
        $this->db->order_by('post_id', 'desc');
        $this->db->limit($limit, $offset);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();

        return $query->result_array();
    }

   // --------------------------------------------------------------------

    /**
     * Return posts for feed.
     *
     * @access   public
     * @param    int
     * @param    int
     * @return   array
     */
    public function feed($limit = 15, $offset = 0)
    {
        $this->db->from('posts');
        $this->db->select('');
        $this->db->order_by('post_id', 'desc');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        return $query->result_array();
    }

   // --------------------------------------------------------------------

    /**
     * Update an existing post.
     *
     * @access   public
     * @param    int
     * @param    array
     */
    public function update($post_id, $data)
    {
        $this->db->where('post_id', $post_id);
        $this->db->update('posts', $data);
    }

   // --------------------------------------------------------------------

    /**
     * Delete a post.
     *
     * @access   public
     * @param    int
     */
    public function delete($post_id)
    {
        $this->db->where('post_id', $post_id);
        $this->db->delete('posts');
    }
}

/* End of file post.php */
/* File location : ./application/models/post.php */