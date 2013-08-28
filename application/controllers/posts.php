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

        $this->load->model('post');

        authenticate_cookie();
        update_session_array();
        try_to_unban();
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

        if ($posts[0])
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
            $file_name = md5(rand());
            $upload_path = './user-content/' . $user_id . '/';

            // Load the Upload library.
            $this->load->library('upload', array(
                'upload_path'   => $upload_path,
                'file_name'     => $file_name,
                'allowed_types' => 'jpg|png|gif',
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

        $response['success'] = TRUE;

        return $response;
    }

    // --------------------------------------------------------------------

    /**
     * Edit an existing post.
     *
     * @access   public
     * @param    string
     */
    public function edit($ajax = FALSE)
    {
        // Check if user is logged in.
        if ($user = $this->session->userdata('user'))
        {
            //Grab stuff from $_POST.
            $post_id = $this->input->post('post_id');
            $content = $this->input->post('content');

            // Check if everything is valid.
            if (is_valid('id', $post_id)
                && is_valid('post', $content)
                && $user['type'] != 'banned')
            {
                // Load Post model.
                $this->load->model('post');

                // Check if the post exists.
                if ($post = $this->post->read($post_id))
                {
                    // Check if the current user made the post.
                    if ($post[0]['user_id'] == $user['user_id'])
                    {
                        // Update the post.
                        $this->post->update($post_id, array(
                            'content'                 => $content,
                            'modified'                => TRUE,
                            'last_modified_timestamp' => gmdate('Y-m-d H:i:s')
                        ));

                        // Output TRUE for AJAX requests.
                        if ($ajax)
                        {
                            $this->output->set_output(TRUE);
                        }
                    }
                }
            }
        }

        // Redirect for non-AJAX requests.
        if ( ! $ajax)
        {
            proceed('pages/feed');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Delete a post.
     *
     * @access   public
     * @param    int      Post id
     * @param    string
     */
    public function delete($post_id = '', $ajax = FALSE)
    {
        // Check if user is logged in.
        if ($user = $this->session->userdata('user'))
        {
            // Check if post exists.
            if ($post = $this->post->read($post_id))
            {
                // Check if the current user made the post.
                if ($post[0]['user_id'] == $user['user_id'])
                {
                    // Delete the post.
                    $this->post->delete($post_id);

                    // Decrese user's reputation by -1.
                    $this->load->model('user');
                    $this->user->update($user['user_id'], array(
                        'reputation' => $user['reputation'] - 1
                    ));

                    // Output TRUE for AJAX requests.
                    if ($ajax)
                    {
                        $this->output->set_output(TRUE);
                    }
                }
            }
        }

        // Redirect for non-AJAX requests.
        if ( ! $ajax)
        {
            proceed('pages/feed');
        }
    }
}

/* End of file posts.php */
/* File location : ./application/controllers/posts.php */