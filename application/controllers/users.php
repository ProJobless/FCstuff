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
     * Authenticate user.
     *
     * @access   public
     * @param    string
     */
    public function login($ajax = FALSE)
    {
        log_access('users', 'login');

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
                // Login the user.
                if (login($user[0]['user_id'], $remember))
                {
                    // Login was successful. Hurray!
                    $this->session->set_flashdata('login_failed', FALSE);

                    // Try to un-ban the user, if banned.
                    try_to_unban();

                    // Output TRUE for AJAX requests.
                    if ($ajax)
                    {
                        $this->output->set_output(TRUE);
                    }
                }
            }
        }

        // Redirect if this isn't an ajax request.
        if ( ! $ajax)
        {
            proceed('/');
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
        log_access('users', 'logout');

        logout();

        // Output TRUE for AJAX requests.
        if ($ajax)
        {
            $this->output->set_output(TRUE);
        }

        // Redirect for non-AJAX requests.
        else
        {
            proceed('/');
        }
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

            // Login the user.
            login($user_id, TRUE);

            // Set a session variable for displaying welcome message.
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
            proceed('/');
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
        log_access('users', 'verify');

        if ($user = $this->user->read($user_id)
            && is_valid('md5', $verification_key))
        {
            if ( ! $user[0]['verified']
                && $user[0]['verification_key'] == $verification_key
                && $user[0]['type'] != 'deleted')
            {
                $this->user->update($user_id, array(
                    'verified'         => TRUE,
                    'verification_key' => md5(rand())
                ));

                // Login user.
                login($user[0]['user_id'], TRUE);

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
            proceed('/');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Recover account.
     *
     * @access   public
     * @param    int      User id
     * @param    string   Recovery key
     * @param    string
     */
    public function recover($user_id = '', $recovery_key = '', $ajax = FALSE)
    {
        log_access('users', 'recover');

        // Get the new password from $_POST;
        $password = $this->input->post('password');

        // Assume that password recovery failed.
        $this->session->set_flashdata('recovery_failed', TRUE);

        // Is everything valid?
        if ($user = $this->user->read($user_id)
            && is_valid('md5', $recovery_key)
            && is_valid('password', $password))
        {
            // Check if the recovery key is valid, and if the user is verified
            // and the account is not deleted.
            if ($user[0]['verified']
                && $user[0]['recovery_key'] == $recovery_key
                && $user[0]['type'] != 'deleted')
            {
                // Recovery was successful. Awesome!
                $this->session->set_flashdata('recovery_failed', FALSE);

                // Update the password and set a new recovery key.
                $this->user->update($user_id, array(
                    'password'     => $this->phpass->hash($password),
                    'recovery_key' => md5(rand())
                ));

                // Login user.
                login($user[0]['user_id'], TRUE);

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
            proceed('/');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Edit profile.
     *
     * @access   public
     * @param    string   Item to edit
     * @param    string
     */
    public function edit($item = '', $ajax = FALSE)
    {
        authenticate_cookie();
        try_to_unban();

        log_access('users', 'edit');

        $content = $this->input->post('content');

        // Is the user logged in?
        if ($user = $this->session->userdata('user'))
        {
            // Get the user id.
            $user_id = $user[0]['user_id'];

            switch ($item)
            {
                case 'username':
                case 'name':
                case 'birthday':
                case 'about_me':
                case 'gender':

                    if (is_valid($item, $content))
                    {
                        $this->user->update($user_id, array(
                            $item => $content
                        ));
                    }

                    break;

                // --------------------------------------------------------

                case 'password':

                    $password = $this->input->post('password');

                    if ($this->phpass->check($password, $user['password']))
                    {
                        if (is_valid('password', $content))
                        {
                            $content = $this->phpass->hash($content);

                            $this->user->update($user_id, array(
                                'password' => $content
                            ));
                        }
                    }

                    break;

                // --------------------------------------------------------

                case 'email':

                    if (is_valid('email', $content))
                    {
                        $this->user->update($user_id, array(
                            'email'            => $content,
                            'verified'         => FALSE,
                            'verification_key' => md5(rand())
                        ));
                    }

                    break;
            }
        }

        update_session_array();

        // Redirect if this isn't an ajax request.
        if ( ! $ajax)
        {
            proceed('/');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Delete an account.
     *
     * @access   public
     * @param    string
     */
    public function delete($ajax = FALSE)
    {
        log_access('users', 'delete');

        // Grab stuff from $_POST.
        $password = $this->input->post('password');
        $captcha  = $this->input->post('captcha');

        // Is the user logged in?
        if ($user = $this->session->userdata('user'))
        {
            // Are the password and captcha correct?
            if ($this->phpass->check($password, $user['password'])
             && is_valid('captcha', $captcha))
            {

                // Load models.
                $this->load->model('post');
                $this->load->model('comment');
                $this->load->model('conversation');
                $this->load->model('cookie');
                $this->load->model('friend');
                $this->load->model('notification');

                $user_id = $user['user_id'];

                // Set user as 'deleted'.
                $this->user->update($user['user_id'], array(
                    'type' => 'deleted'
                ));

                // Delete content uploaded by the user.
                $this->load->helper('file');
                delete_files('user-content/' . $user['user_id'] .'/');

                // Delete all posts made by the user.
                $this->post->delete_all($user_id);

                // Delete all comments made by the user.
                $this->comment->delete_all($user_id);

                // Delete all conversations of the user.
                $this->conversation->delete_all($user_id);

                // Invalidate all authentication cookies.
                $this->cookie->delete_all($user_id);

                // Delete all friends.
                $this->friend->delete_all($user_id);

                // Delete all notifications for the user.
                $this->notification->delete_all($user_id);

                // Logout user.
                logout();

                // Invalidate the used captcha.
                $this->session->unset_userdata('captcha');

                // Output TRUE for AJAX requests.
                if ($ajax)
                {
                    $this->output->set_output(TRUE);
                }
            }

            else
            {
                $this->session->set_flashdata('invalid_password', TRUE);
            }
        }

        // Redirect if this isn't an ajax request.
        if ( ! $ajax)
        {
            proceed('/');
        }
    }
}

/* End of file users.php */
/* File location : ./application/controllers/users.php */