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
     * Handle an authentication request.
     *
     * @access   public
     * @param    string
     */
    public function login($ajax = FALSE)
    {
        log_access('users', 'login');

        // Logout previously logged in users.
        logout();

        // Grab stuff from $_POST.
        $identifier = $this->input->post('identifier');
        $password   = $this->input->post('password');
        $remember   = $this->input->post('remember');

        // Was the authentication successful?
        if ($user = $this->_login($identifier, $password, $remember))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
            $this->session->set_flashdata('login_failed', TRUE);
        }

        // Redirect if this isn't an AJAX request.
        if ( ! $ajax)
        {
            proceed('/');
        }

        // Return a JSON array for AJAX requests.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Authenticate a user.
     *
     * @access   private
     * @param    string    User identifier (user_id, email or username)
     * @param    string    Password
     * @param    bool      Flag for setting authentication cookie
     * @return   bool      TRUE, if authentication was successful
     */
    private function _login($identifier, $password, $remember)
    {
        // Check if the user exists.
        if ( ! $user = $this->user->read($identifier))
        {
            return FALSE;
        }

        // Check if the password is correct.
        if ( ! $this->phpass->check($password, $user[0]['password']))
        {
            return FALSE;
        }

        // Check if login was successful.
        if ( ! login($user[0]['user_id'], $remember))
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle a logout request.
     *
     * @access   public
     * @param    string
     */
    public function logout($ajax = FALSE)
    {
        log_access('users', 'logout');

        // Logout the user.
        logout();

        // Redirect if this isn't an AJAX request.
        if ( ! $ajax)
        {
            proceed('/');
        }

        // Return a JSON array for AJAX requests.
        $response['success'] = TRUE;
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for viewing data about a user.
     *
     * @access   public
     * @param    string   Identifier
     */
    public function view($identifier = '')
    {
        log_access('users', 'view');

        // If an identifier is not provided and a user is logged in, set the
        // identifier as the logged in user's user_id.
        if (empty($identifier) && $user = $this->session->userdata('user'))
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

        // Output a JSON array.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
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
     * @access   private
     * @param    int      User id
     * @param    string   The verification key
     * @param    string
     */
    private function _verify($user_id = '', $verification_key = '', $ajax = FALSE)
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
     * @access   private
     * @param    int      User id
     * @param    string   Recovery key
     * @param    string
     */
    private function _recover($user_id = '', $recovery_key = '', $ajax = FALSE)
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