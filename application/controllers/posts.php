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
    }

    // --------------------------------------------------------------------

    /**
     * Create a new post.
     *
     * @access   public
     * @param    string
     */
    public function create($ajax = FALSE)
    {
        log_access('posts', 'create');

        // Get the post content from $_POST.
        $content = $this->input->post('content');

        // Set default values.
        $response = array(
            'success' => '',
            'wait'    => ''
        );
        $file_name = '';
        $proceed = TRUE;

        // Is the user logged in?
        if ($user = $this->session->userdata('user'))
        {
            $user_id = $user['user_id'];

            // Is the post content valid and the user not banned?
            if (is_valid('post', $content) && $user['type'] != 'banned')
            {
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
                        $proceed = FALSE;
                        $response['wait'] = 15 - $diff;
                    }
                }

                // Has the user uploaded an image with the post?
                if ($proceed && isset($_FILES['image']))
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
                        $proceed = FALSE;
                    }
                }

                // Is everything fine till now?
                if ($proceed)
                {
                    // Insert the new post in the database.
                    $this->post->create(array(
                        'user_id' => $user['user_id'],
                        'content' => $content,
                        'image'   => $file_name
                    ));

                    $response['success'] = TRUE;
                }
            }
        }

        // Respond with JSON for AJAX requests.
        if ($ajax)
        {
            $this->output->set_output(json_encode($response));
        }

        // Redirect for non-AJAX requests.
        else
        {
            proceed('pages/feed');
        }
    }
}

/* End of file posts.php */
/* File location : ./application/controllers/posts.php */