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
        $this->load->model('notification');

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

        if (isset($comments[0]))
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

        update_session_array();

        $this->notification->create(array(
            'user_id'  => $user['user_id'],
            'content'  => 'You got +2 reputation.',
            'image'    => '',
            'link'     => 'people/me'
        ));

        if ( ! ($user['user_id'] == $post[0]['user_id']))
        {
            $this->notification->create(array(
                'user_id'  => $post[0]['user_id'],
                'content'  => substr($user['name'] . ' added a new comment on your post : ' . $content, 0, 500),
                'image'    => $user['user_id'] . '/' . $user['profile_picture'],
                'link'     => 'posts/' . $post[0]['post_id']
            ));
        }

        $response['success'] = TRUE;

        return $response;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for editing comments.
     *
     * @access   public
     * @param    string
     */
    public function edit($ajax = FALSE)
    {
        log_access('comments', 'edit');

        $comment_id = $this->input->post('comment_id');
        $content    = $this->input->post('content');

        if ($this->_edit($comment_id, $content))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            $comment = $this->comment->read($comment_id);

            if ($post_id = $comment[0]['post_id'])
            {
                proceed('posts/' . $post_id);
            }

            else
            {
                proceed('/');
            }
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Edit an existing comment.
     *
     * @access   private
     * @param    int       Comment id
     * @param    string    New content
     * @return   bool
     */
    private function _edit($comment_id, $content)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ( ! is_valid('comment', $content))
        {
            return FALSE;
        }

        if ( ! ($comment = $this->comment->read($comment_id)))
        {
            return FALSE;
        }

        if ($comment[0]['user_id'] != $user['user_id'])
        {
            return FALSE;
        }

        $this->comment->update($comment_id, array(
            'content'                 => $content,
            'modified'                => TRUE,
            'last_modified_timestamp' => gmdate('Y-m-d H:i:s')
        ));

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for deleting comments.
     *
     * @access   public
     * @param    string
     */
    public function delete($ajax = FALSE)
    {
        log_access('comments', 'delete');

        $comment_id = $this->input->post('comment_id');

        if ($this->_delete($comment_id))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            proceed('/');
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Delete a comment.
     *
     * @access   private
     * @param    int       Comment id
     * @return   bool
     */
    private function _delete($comment_id)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ( ! ($comment = $this->comment->read($comment_id)))
        {
            return FALSE;
        }

        if ( ! ($comment[0]['user_id'] == $user['user_id']))
        {
            return FALSE;
        }

        $this->comment->delete($comment_id);

        return TRUE;
    }
}

/* End of file comments.php */
/* File location : ./application/controllers/comments.php */