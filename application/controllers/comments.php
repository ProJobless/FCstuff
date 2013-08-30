<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * COMMENTS CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Comments extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->model('post');
        $this->load->model('comment');

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for creating a new comment.
     *
     * @access   public
     * @param    string
     */
    public function create($ajax = FALSE)
    {
        log_access('comments', 'create');

        $post_id = $this->input->post('post_id');
        $content = $this->input->post('content');

        $response = $this->_create($post_id, $content);

        if ( ! $ajax)
        {
            proceed('posts/' . $post_id);
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Create a new comment.
     *
     * @access   private
     * @param    int       Post id
     * @param    string    Content
     * @return   array     Response array
     */
    private function _create($post_id, $content)
    {
        $response['success'] = FALSE;

        if ( ! ($user = $this->session->userdata('user')))
        {
            return $response;
        }

        if ($user['type'] == 'banned')
        {
            return $response;
        }

        if ( ! ($post = $this->post->read($post_id)))
        {
            return $response;
        }

        if ( ! is_valid('comment', $content))
        {
            return $response;
        }

        $comments = $this->comment->user($user['user_id']);

        if ($comments[0])
        {
            $last_comment_time = strtotime($comments[0]['timestamp']);
            $current_time      = strtotime(gmdate('Y-m-d H:i:s'));

            $diff = $current_time - $last_comment_time;

            if ($diff <= 5)
            {
                $response['wait'] = 5 - $diff;

                return $response;
            }
        }

        $this->comment->create(array(
            'post_id' => $post_id,
            'user_id' => $user['user_id'],
            'content' => $content
        ));

        $this->post->update($post_id, array(
            'last_comment_timestamp' => gmdate('Y-m-d H:i:s'),
            'comments'               => $post[0]['comments'] + 1
        ));

        $this->user->update($user['user_id'], array(
            'reputation' => $user['reputation'] + 2
        ));

        $response['success'] = TRUE;
        return $response;
    }
}

/* End of file comments.php */
/* File location : ./application/controllers/comments.php */