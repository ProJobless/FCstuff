<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AUTHENTICATION CONTROLLER
 *
 * @package    FCstuff
 * @category   Authentication
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Authentication extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->library('phpass');
    }

    // --------------------------------------------------------------------

    /**
     * Authenticate user.
     *
     * @access   public
     * @param    string
     */
    public function login($ajax = FALSE)
    {
        log_access('authentication', 'login');

        // Remove user array from $_SESSION.
        $this->session->unset_userdata('user');

        // Remove authentication cookies.
        delete_cookie();

        // Assume that the login was unsuccessful.
        $this->session->set_flashdata('login_failed', TRUE);

        // Grab stuff from $_POST.
        $identifier = $this->input->post('identifier');
        $password   = $this->input->post('password');
        $remember   = $this->input->post('remember');

        // Does the user exist?
        if ($identifier && $password && $user = $this->user->read($identifier))
        {
            // Is the password correct?
            if ($this->phpass->check($password, $user[0]['password']))
            {
                // Login was successful. Hurray!
                $this->session->set_flashdata('login_failed', FALSE);

                // Set user data as session array.
                $this->session->set_userdata('user', $user[0]);

                // Update the last seen timestamp.
                update_last_seen_timestamp();

                // Set an authentication cookie.
                if ($remember)
                {
                    set_cookie($user[0]['user_id']);
                }

                // Output TRUE for AJAX requests.
                if ($ajax)
                {
                    $this->output->set_output(TRUE);
                }
            }
        }

        // Redirect if this isn't an ajax request.
        if ( ! $ajax)
        {
            // Is an URL provided?
            if ($url = $this->input->get('continue'))
            {
                redirect($url);
            }

            // Redirect to home if an URL isn't provided.
            else
            {
                redirect('/');
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Logout user.
     *
     * @access   public
     * @param    string
     */
    public function logout($ajax = FALSE)
    {
        log_access('authentication', 'logout');

        // Update the last seen timestamp.
        update_last_seen_timestamp();

        // Remove user array from $_SESSION.
        $this->session->unset_userdata('user');

        // Remove cookies.
        delete_cookie();

        // Output TRUE for AJAX requests.
        if ($ajax)
        {
            $this->output->set_output(TRUE);
        }

        // Else, proceed to the provided URL.
        elseif ($url = $this->input->get('continue'))
        {
            redirect($url);
        }

        // Else, proceed to home.
        else
        {
            redirect('/');
        }
    }
}

/* End of file authentication.php */
/* File location : ./application/controllers/authentication.php */