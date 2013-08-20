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
        $this->load->helper('user');
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
                'username'         => generate_username($email),
                'email'            => $email,
                'password'         => $this->phpass->hash($password),
                'name'             => ucwords(strtolower($name)),
                'verification_key' => md5(rand()),
                'recovery_key'     => md5(rand()),
                'last_seen'        => gmdate('Y-m-d H:i:s')
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
            mkdir('user-content/' . $user_id, 0777, TRUE);

            $this->user->update($user_id, array(
                'profile_picture' => generate_profile_picture($user_id)
            ));

            // Set an authentication cookie.
            set_cookie($user_id);

            // Output TRUE if this is an AJAX request.
            if ($ajax)
            {
                $this->output->set_output(TRUE);
            }
        }

        // Redirect to home if this isn't an ajax request.
        if ( ! $ajax)
        {
            redirect('/');
        }
    }
}

/* End of file users.php */
/* File location : ./application/controllers/users.php */