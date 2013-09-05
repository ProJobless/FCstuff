<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PAGES CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Pages extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Display the landing page.
     *
     * @access   public
     */
    public function landing()
    {
        log_access('pages', 'landing');

        if ( ! $this->session->userdata('user'))
        {
            $this->load->view('landing');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Display user profile.
     *
     * @access   public
     * @param    string
     * @param    string
     */
    public function profile($identifier = '', $ajax = FALSE)
    {
        log_access('pages', 'profile');

        $this->load->model('user');

        if ($identifier == 'me' && $user = $this->session->userdata('user'))
        {
            $identifier = $user['user_id'];
        }

        if ($user = $this->_profile($identifier))
        {
            $response['success'] = TRUE;
            $response['user']    = $user;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ($ajax)
        {
            // Output a JSON array for AJAX requests.
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($response));
        }

        else
        {
            // Display a page showing the user's profile.
        }
    }

    /**
     * Return user data.
     *
     * @access   private
     * @param    string    User identifier
     * @return   array
     */
    private function _profile($identifier)
    {
        // Does the user exist?
        if ( ! ($user = $this->user->read($identifier)))
        {
            return FALSE;
        }

        // Remove sensitive information.
        unset($user[0]['password']);
        unset($user[0]['verification_key']);
        unset($user[0]['recovery_key']);
        unset($user[0]['unsubscribed']);
        unset($user[0]['unsubscription_key']);
        unset($user[0]['verification_key']);
        unset($user[0]['timestamp']);
        unset($user[0]['email']);
        unset($user[0]['type']);
        unset($user[0]['last_seen']);

        return $user[0];
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for displaying the feed.
     *
     * @access   public
     * @param    int      Last post id
     * @param    string
     */
    public function feed($last_post_id = FALSE, $ajax = FALSE)
    {
        if ($feed = $this->_feed($last_post_id))
        {
            $response['success'] = TRUE;
            $response['feed']    = $feed;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ($ajax)
        {
            // Output a JSON array for AJAX requests.
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($response));
        }

        else
        {
            // Display a page showing the feed.
        }
    }

    /**
     * Return feed contents.
     *
     * @access   private
     * @param    int       Last post id
     * @return   array
     */
    private function _feed($last_post_id = FALSE)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ($user['type'] == 'banned')
        {
            return FALSE;
        }

        if ($last_post_id && ! is_numeric($last_post_id))
        {
            return FALSE;
        }

        $this->load->model('post');

        $feed = $this->post->feed($last_post_id);

        return $feed;
    }
}

/* End of file pages.php */
/* File location : ./application/controllers/pages.php */