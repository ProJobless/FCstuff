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

        // Does the user exist?
        if ($user = $this->user->read($identifier))
        {
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

            // Set the response array.
            $response['success'] = TRUE;
            $response['user']    = $user[0];
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
}

/* End of file pages.php */
/* File location : ./application/controllers/pages.php */