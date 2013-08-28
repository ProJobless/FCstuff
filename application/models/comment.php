<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * COMMENT MODEL
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Comment extends CI_Model {

    /**
     * Create a new comment.
     *
     * @access   public
     * @param    array
     */
    public function create($comment)
    {
        $this->db->insert('comments', $comment);
    }

    // --------------------------------------------------------------------

    /**
     * Return comments for a post.
     *
     * @access   public
     * @param    int
     * @param    int
     * @return   array
     */
    public function read($post_id, $last_comment_id = FALSE)
    {
        $this->db->query("SET time_zone = '+00:00'");
        $this->db->from('comments');
        $this->db->select('comment_id, post_id user_id, content, timestamp, modified, last_modified_timestamp');
        $this->db->limit(15);
        $this->db->where('post_id', $post_id);
        if ($last_comment_id) {
            $this->db->where('comment_id <', $last_comment_id);
        }
        $this->db->order_by('comment_id', 'desc');
        $query = $this->db->get();

        return $query->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * Update an existing comment.
     *
     * @access   public
     * @param    int
     * @param    array
     */
    public function update($comment_id, $data)
    {
        $this->db->where('comment_id', $comment_id);
        $this->db->limit(1);
        $this->db->update('comments', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Delete a comment.
     *
     * @access   public
     * @param    int
     */
    public function delete($comment_id)
    {
        $this->db->where('comment_id', $comment_id);
        $this->db->limit(1);
        $this->db->delete('comments');
    }

    // --------------------------------------------------------------------

    /**
     * Delete all comments made by a user.
     *
     * @access   public
     * @param    int
     */
    public function delete_all($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('comments');
    }
}

/* End of file comment.php */
/* File location : ./application/models/comment.php */