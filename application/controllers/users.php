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
        $this->load->model('notification');
        $this->load->library('phpass');
    }

    // --------------------------------------------------------------------

    /**
     * Handle AJAX requests for displaying user data.
     *
     * @access   public
     * @param    string
     */
    public function read($identifier = '')
    {
        log_access('users', 'read');

        if ($identifier == 'me' && $user = $this->session->userdata('user'))
        {
            $identifier = $user['user_id'];
        }

        if ($user = $this->_read($identifier))
        {
            $response['success'] = TRUE;
            $response['user']    = $user;
        }

        else
        {
            $response['success'] = FALSE;
        }

        // Output a JSON array for AJAX requests.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Return data about a user.
     *
     * @access   private
     * @param    string    User identifier
     * @return   array
     */
    private function _read($identifier)
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
        unset($user[0]['last_seen']);

        return $user[0];
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
            if ( ! $response['success'])
            {
                proceed('/#login');
            }

            else
            {
                proceed('/');
            }
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
     * Handle requests for creating a new user.
     *
     * @access   public
     * @param    string
     */
    public function create($ajax = FALSE)
    {
        log_access('users', 'create');

        // Logout previously logged in users.
        logout();

        // Grab stuff from $_POST.
        $name     = $this->input->post('name');
        $email    = $this->input->post('email');
        $password = $this->input->post('password');
        $captcha  = $this->input->post('captcha');

        // Was the account successfully created?
        if ($this->_create($name, $email, $password, $captcha))
        {
            $response['success'] = TRUE;
            $this->session->set_flashdata('welcome', TRUE);
        }

        else
        {
            $response['success'] = FALSE;
        }

        // Invalidate the used captcha.
        $this->session->unset_userdata('captcha');

        // Redirect if this isn't an AJAX request.
        if ( ! $ajax)
        {
            proceed('/');
        }

        // Output a JSON array for AJAX requests.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Create a new user account.
     *
     * @access   private
     * @param    string    Name
     * @param    string    Email address
     * @param    string    Password
     * @param    string    Captcha
     * @return   bool
     */
    private function _create($name, $email, $password, $captcha)
    {
        // Set the name and email as temporary session variables.
        $this->session->set_flashdata('name', $name);
        $this->session->set_flashdata('email', $email);

        // Check if the name is valid.
        if ( ! is_valid('name', $name))
        {
            return FALSE;
        }

        // Check if the email address is valid.
        if ( ! is_valid('email', $email))
        {
            return FALSE;
        }

        // Check if the password is valid.
        if ( ! is_valid('password', $password))
        {
            return FALSE;
        }

        // Check if the captcha is valid.
        if ( ! is_valid('captcha', $captcha))
        {
            return FALSE;
        }

        // Add the user to database.
        $this->user->create(array(
            'username'           => generate_username($email),
            'email'              => $email,
            'password'           => $this->phpass->hash($password),
            'name'               => ucwords(strtolower($name)),
            'reputation'         => 50,
            'verification_key'   => md5(rand()),
            'recovery_key'       => md5(rand()),
            'unsubscription_key' => md5(rand()),
            'last_seen'          => gmdate('Y-m-d H:i:s')
        ));

        // Get the user_id.
        $user = $this->user->read($email);
        $user_id = $user[0]['user_id'];

        // Generate a profile picture.
        if ( ! file_exists('user-content/' . $user_id))
        {
            mkdir('user-content/' . $user_id, 0777, TRUE);
        }

        // Update user data.
        $this->user->update($user_id, array(
            'profile_picture' => generate_profile_picture($user_id)
        ));

        $this->notification->create(array(
            'user_id'  => $user_id,
            'content'  => 'Welcome to FCstuff. Make a new post to get started.',
            'link'     => ''
        ));

        $this->notification->create(array(
            'user_id'  => $user_id,
            'content'  => 'You got +50 reputation.',
            'image'    => '',
            'link'     => 'people/me'
        ));

        // Login the user.
        login($user_id, TRUE);

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for verifying a user account.
     *
     * @access   public
     * @param    int      User id
     * @param    string   The verification key
     */
    public function verify($user_id = '', $key = '')
    {
        log_access('users', 'verify');

        logout();

        if ($this->_verify($user_id, $key))
        {
            login($user_id, TRUE);
        }

        proceed('/');
    }

    /**
     * Verify account.
     *
     * @access   private
     * @param    int       User id
     * @param    string    Verification key
     * @return   bool
     */
    private function _verify($user_id, $key)
    {
        if ( ! is_valid('id', $user_id))
        {
            return FALSE;
        }

        if ( ! is_valid('md5', $key))
        {
            return FALSE;
        }

        if ( ! ($user = $this->user->read($user_id)))
        {
            return FALSE;
        }

        if ($user[0]['type'] == 'deleted')
        {
            return FALSE;
        }

        if ($user[0]['verification_key'] != $key)
        {
            return FALSE;
        }

        $this->user->update($user_id, array(
            'verified'         => TRUE,
            'verification_key' => md5(rand())
        ));

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for recovering user accounts.
     *
     * @access   public
     * @param    string
     */
    public function recover($ajax = FALSE)
    {
        log_access('users', 'recover');

        logout();

        $user_id      = $this->input->post('user_id');
        $recovery_key = $this->input->post('recovery_key');
        $password     = $this->input->post('password');

        if ($this->_recover($user_id, $recovery_key, $password))
        {
            login($user_id, TRUE);
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
     * Recover a user account.
     *
     * @access   private
     * @param    int       User id
     * @param    string    Recovery key
     * @param    string    New password
     * @return   bool
     */
    private function _recover($user_id, $key, $password)
    {
        if ( ! is_valid('id', $user_id))
        {
            return FALSE;
        }

        if ( ! is_valid('md5', $key))
        {
            return FALSE;
        }

        if ( ! is_valid('password', $password))
        {
            return FALSE;
        }

        if ( ! ($user = $this->user->read($user_id)))
        {
            return FALSE;
        }

        if ($user[0]['type'] == 'deleted')
        {
            return FALSE;
        }

        if ( ! $user[0]['verified'])
        {
            return FALSE;
        }

        if ($user[0]['recovery_key'] != $key)
        {
            return FALSE;
        }

        $this->user->update($user_id, array(
            'password'     => $this->phpass->hash($password),
            'recovery_key' => md5(rand())
        ));

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for editing user data.
     *
     * @access   public
     * @param    string
     */
    public function edit($ajax = FALSE)
    {
        log_access('users', 'edit');

        authenticate_cookie();
        try_to_unban();

        $content = $this->input->post('content');
        $content_type = $this->input->post('content_type');

        if ($this->_edit($content_type, $content))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            proceed('people/me');
        }

        // Return a JSON array for AJAX requests.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Edit user data.
     *
     * @access   private
     * @param    string    Type of content
     * @param    string    The new content
     */
    private function _edit($content_type, $content)
    {
        // Check if the user is logged in.
        if ( ! $user = $this->session->userdata('user'))
        {
            return FALSE;
        }

        // Get the user id.
        $user_id = $user['user_id'];

        switch ($content_type)
        {
            case 'username':
            case 'name':
            case 'birthday':
            case 'about_me':

                if ( ! is_valid($content_type, $content))
                {
                    return FALSE;
                }

                $this->user->update($user_id, array(
                    $content_type => $content
                ));

                break;

            // --------------------------------------------------------

            case 'password':

                $password = $this->input->post('password');

                if ( ! $this->phpass->check($password, $user['password']))
                {
                    return FALSE;
                }

                if ( ! is_valid('password', $content))
                {
                    return FALSE;
                }

                $this->user->update($user_id, array(
                    'password' => $this->phpass->hash($content)
                ));

                break;

            // --------------------------------------------------------

            case 'email':

                if ( ! is_valid('email', $content))
                {
                    return FALSE;
                }

                $this->user->update($user_id, array(
                    'email'            => $content,
                    'verified'         => FALSE,
                    'verification_key' => md5(rand())
                ));

                break;

            // --------------------------------------------------------

            case 'profile_picture':

                if ( ! isset($_FILES['image']))
                {
                    return FALSE;
                }

                $file_name = md5(rand() * time()) . '.jpg';
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
                    return FALSE;
                }

                $file_name = process_profile_picture($file_name);

                // Update user data.
                $this->user->update($user_id, array(
                    'profile_picture' => $file_name
                ));

                break;

            // --------------------------------------------------------

            default:

                return FALSE;
        }

        update_session_array();

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for deleting an account.
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

        if ($this->_delete($password, $captcha))
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

        // Invalidate the used captcha.
        $this->session->unset_userdata('captcha');

        // Return a JSON array for AJAX requests.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Delete an account.
     *
     * @access   private
     * @param    string
     * @param    string
     * @return   bool
     */
    private function _delete($password, $captcha)
    {
        // Is the user logged in?
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ( ! $this->phpass->check($password, $user['password']))
        {
            $this->session->set_flashdata('invalid_password', TRUE);
            return FALSE;
        }

        if ( ! is_valid('captcha', $captcha))
        {
            return FALSE;
        }

        $user_id = $user['user_id'];

        // Load models.
        $this->load->model('post');
        $this->load->model('comment');
        $this->load->model('conversation');
        $this->load->model('cookie');
        $this->load->model('friend');
        $this->load->model('notification');

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

        $this->user->update($user_id, array(
            'password'           => NULL,
            'reputation'         => 0,
            'posts'              => 0,
            'friends'            => 0,
            'type'               => 'deleted',
            'verification_key'   => NULL,
            'recovery_key'       => NULL,
            'unsubscription_key' => NULL,
            'birthday'           => NULL,
            'about_me'           => NULL,
            'profile_picture'    => NULL
        ));

        // Logout user.
        logout();

        return TRUE;
    }
}

/* End of file users.php */
/* File location : ./application/controllers/users.php */