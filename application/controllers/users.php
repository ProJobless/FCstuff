<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * USERS CONTROLLER
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Users extends CI_Controller {

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
     * Create a new user.
     *
     * @access   public
     * @param    string
     */
    public function create($ajax = FALSE)
    {
        log_access('users', 'create');

        // Remove user array from $_SESSION.
        $this->session->unset_userdata('user');

        // Remove authentication cookies.
        delete_cookie();

        // Grab stuff from $_POST.
        $name     = $this->input->post('name');
        $email    = $this->input->post('email');
        $password = $this->input->post('password');
        $captcha  = $this->input->post('captcha');

        // Is everything valid?
        if (is_valid('name', $name)
            && is_valid('email', $email)
            && is_valid('password', $password)
            && is_valid('captcha', $captcha))
        {
            $data = array(
                'username'           => generate_username($email),
                'email'              => $email,
                'password'           => $this->phpass->hash($password),
                'name'               => ucwords(strtolower($name)),
                'verification_key'   => md5(rand()),
                'recovery_key'       => md5(rand()),
                'unsubscription_key' => md5(rand()),
                'last_seen'          => gmdate('Y-m-d H:i:s')
            );

            // Add the user to database.
            $this->user->create($data);

            // Get the user_id.
            $user = $this->user->read($email);
            $user_id = $user[0]['user_id'];

            // Set user data as session array.
            $this->session->set_userdata('user', $user[0]);

            // Invalidate the used captcha.
            $this->session->unset_userdata('captcha');

            // Generate a profile picture.
            if ( ! file_exists('user-content/' . $user_id))
            {
                mkdir('user-content/' . $user_id, 0777, TRUE);
            }

            $this->user->update($user_id, array(
                'profile_picture' => generate_profile_picture($user_id)
            ));

            // Set an authentication cookie.
            set_cookie($user_id);

            // Set a session variable for welcome message.
            $this->session->set_flashdata('welcome', TRUE);

            // Output TRUE for AJAX requests.
            if ($ajax)
            {
                $this->output->set_output(TRUE);
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
     * Verify account.
     *
     * @access   public
     * @param    int      User id
     * @param    string   The verification key
     * @param    string
     */
    public function verify($user_id = '', $verification_key = '', $ajax = FALSE)
    {
        if (is_valid('user_id', $user_id)
            && is_valid('md5', $verification_key))
        {
            $user = $this->user->read($user_id);

            if ( ! $user[0]['verified']
                && $user[0]['verification_key'] == $verification_key
                && $user[0]['type'] != 'deleted')
            {
                $this->user->update($user_id, array(
                    'verified'         => TRUE,
                    'verification_key' => md5(rand())
                ));

                // Set user data as session array.
                $this->session->set_userdata('user', $user[0]);

                // Update the last seen timestamp.
                update_last_seen_timestamp();

                // Set an authentication cookie.
                set_cookie($user[0]['user_id']);

                // Output TRUE for AJAX requests.
                if ($ajax)
                {
                    $this->output->set_output(TRUE);
                }
            }
        }

        log_access('users', 'verify');

        update_last_seen_timestamp();

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
}

/* End of file users.php */
/* File location : ./application/controllers/users.php */