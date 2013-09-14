<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POSTS CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Posts extends CI_Controller {

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
        $this->load->model('notification');
        $this->load->model('rating');

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Handle AJAX requests for displaying feed.
     *
     * @access   public
     */
    public function feed()
    {
        log_access('posts', 'feed');

        $last_post_id = $this->input->post('last_post_id');

        $feed = $this->_feed($last_post_id);

        if (count($feed) == 0)
        {
            $response['success'] = FALSE;
        }

        else
        {
            $response['success'] = TRUE;
            $response['feed']    = $feed;
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Return feed.
     *
     * @access   private
     * @param    int       Last post id
     * @return   array
     */
    private function _feed($last_post_id)
    {
        $feed_array = $this->post->feed($last_post_id);

        $user_id = FALSE;
        if ($user = $this->session->userdata('user'))
        {
            $user_id = $user['user_id'];
        }

        for ($i = 0; $i < count($feed_array); $i++)
        {
            $post_id = $feed_array[$i]['post_id'];
            $rating_array = $this->rating->read($post_id, $user_id);
            $rating = '0';
            if (count($rating_array) > 0)
            {
                $rating = $rating_array[0]['rating'];
            }
            $feed_array[$i]['rating'] = $rating;
        }

        return $feed_array;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for making new posts.
     *
     * @access   public
     * @param    string
     */
    public function create($ajax = FALSE)
    {
        log_access('posts', 'create');

        // Get the post content from $_POST.
        $content = $this->input->post('content');

        $response = $this->_create($content);

        if ( ! $ajax)
        {
            proceed('/');
        }

        // Return a JSON array for AJAX requests.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Create a new post.
     *
     * @access   private
     * @param    string    Content of the post
     * @return   array
     */
    private function _create($content)
    {
        $response['success'] = FALSE;

        // Is the user logged in?
        if ( ! ($user = $this->session->userdata('user')))
        {
            return $response;
        }

        if ($user['type'] == 'banned')
        {
            return $response;
        }

        $user_id = $user['user_id'];

        if ( ! is_valid('post', $content))
        {
            return $response;
        }

        // Get array of posts made by the user.
        $posts = $this->post->user($user_id);

        if (isset($posts[0]))
        {
            $last_post_timestamp = strtotime($posts[0]['timestamp']);
            $current_timestamp   = strtotime(gmdate('Y-m-d H:i:s'));

            $diff = $current_timestamp - $last_post_timestamp;

            // Was the last post made in the last 15 seconds?
            if ($diff <= 15)
            {
                $response['wait'] = 15 - $diff;

                return $response;
            }
        }

        $file_name = '';

        // Has the user uploaded an image with the post?
        if (isset($_FILES['image']))
        {
            $file_name = md5(rand()) . '.jpg';
            $upload_path = './user-content/' . $user_id . '/';

            // Load the Upload library.
            $this->load->library('upload', array(
                'upload_path'   => $upload_path,
                'file_name'     => $file_name,
                'allowed_types' => 'jpg|jpeg',
                'max_size'      => '1024'
            ));

            // Did the upload fail?
            if ( ! $this->upload->do_upload('image'))
            {
                return $response;
            }
        }

        // Insert the new post in the database.
        $this->post->create(array(
            'user_id' => $user['user_id'],
            'content' => $content,
            'image'   => $file_name
        ));

        $this->user->update($user['user_id'], array(
            'posts'      => $user['posts'] + 1,
            'reputation' => $user['reputation'] + 5
        ));

        $this->notification->create(array(
            'user_id'  => $user['user_id'],
            'content'  => 'You got +5 reputation.',
            'image'    => '',
            'link'     => 'people/me'
        ));

        update_session_array();

        $response['success'] = TRUE;

        return $response;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for editing a post.
     *
     * @access   public
     * @param    string
     */
    public function edit($ajax = FALSE)
    {
        log_access('posts', 'edit');

        $post_id = $this->input->post('post_id');
        $content = $this->input->post('content');

        if ($this->_edit($post_id, $content))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            proceed('posts/' . $post_id);
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Edit an existing post.
     *
     * @access   private
     * @param    string    Post id
     * @param    string    New content
     * @return   bool
     */
    private function _edit($post_id, $content)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ($user['type'] == 'banned')
        {
            return FALSE;
        }

        if ( ! is_valid('id', $post_id))
        {
            return FALSE;
        }

        if ( ! is_valid('post', $content))
        {
            return FALSE;
        }

        if ( ! ($post = $this->post->read($post_id)))
        {
            return FALSE;
        }

        if ($post[0]['user_id'] != $user['user_id'])
        {
            return FALSE;
        }

        $this->post->update($post_id, array(
            'content'                 => $content,
            'modified'                => TRUE,
            'last_modified_timestamp' => gmdate('Y-m-d H:i:s')
        ));

        $this->user->update($user['user_id'], array(
            'reputation' => $user['reputation'] - 2
        ));

        update_session_array();

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Delete a post.
     *
     * @access   public
     * @param    string
     */
    public function delete($ajax = FALSE)
    {
        log_access('posts', 'delete');

        $post_id = $this->input->post('post_id');

        if ($this->_delete($post_id))
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
     * Delete a post.
     *
     * @access   private
     * @param    int       Post id
     * @return   bool
     */
    private function _delete($post_id)
    {
        if ( ! is_valid('id', $post_id))
        {
            return FALSE;
        }

        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ( ! ($post = $this->post->read($post_id)))
        {
            return FALSE;
        }

        if ($post[0]['user_id'] != $user['user_id'])
        {
            return FALSE;
        }

        $this->post->delete($post_id);

        $this->user->update($user['user_id'], array(
            'reputation' => $user['reputation'] - 5
        ));

        update_session_array();

        return TRUE;
    }
}

/* End of file posts.php */
/* File location : ./application/controllers/posts.php */