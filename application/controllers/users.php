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
        // Get the new password from $_POST;
        $password = $this->input->post('password');

        // Is everything valid?
        if (is_valid('user_id', $user_id)
            && is_valid('md5', $recovery_key)
            && is_valid('password', $password))
        {
            // Get user data.
            $user = $this->user->read($user_id);

            // Check if the recovery key is valid, and if the user is verified
            // and the account is not deleted.
            if ($user[0]['verified']
                && $user[0]['recovery_key'] == $recovery_key
                && $user[0]['type'] != 'deleted')
            {
                // Update the password and set a new recovery key.
                $this->user->update($user_id, array(
                    'password'     => $this->phpass->hash($password),
                    'recovery_key' => md5(rand())
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
        $content = $this->input->post('content');

        // Is the user logged in?
        if ($user = $this->session->userdata('user'))
        {
            // Get the user id.
            $user_id = $user[0]['user_id'];

            switch ($item)
            {
                case 'username':

                    if (is_valid('username', $content))
                    {
                        $this->user->update($user_id, array(
                            'username' => $content
                        ));
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

                // --------------------------------------------------------

                case 'password':

                    if (is_valid('password', $content))
                    {
                        $password = $this->phpass->hash($content);

                        $this->user->update($user_id, array(
                            'password' => $password
                        ));
                    }

                // --------------------------------------------------------

                case 'name':

                    if ($is_valid('name', $content))
                    {
                        $this->user->update($user_id, array(
                            'name' => $content
                        ));
                    }

                // --------------------------------------------------------

                case 'birthday':

                    if ($is_valid('birthday', $content))
                    {
                        $this->user->update($user_id, array(
                            'birthday' => $content
                        ));
                    }

                // --------------------------------------------------------

                case 'about_me':

                    if ($is_valid('about_me', $content))
                    {
                        $this->user->update($user_id, array(
                            'about_me' => $content
                        ));
                    }

                // --------------------------------------------------------

                case 'gender':

                    if ($is_valid('gender', $content))
                    {
                        $this->user->update($user_id, array(
                            'gender' => $content
                        ));
                    }
            }
        }

        log_access('users', 'edit');

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

        update_last_seen_timestamp();

        // Get password from $_POST.
        $password = $this->input->post('password');

        // Is the user logged in?
        if ($user = $this->session->userdata('user'))
        {
            // Is the password correct?
            if ($this->phpass->check($password, $user['password']))
            {
                // Set user as 'deleted'.
                $this->user->update($user['user_id'], array(
                    'type' => 'deleted'
                ));

                // Delete content uploaded by the user.
                $this->load->helper('file');
                delete_files('user-content/' . $user['user_id'] .'/');

                // Remove user array from $_SESSION.
                $this->session->unset_userdata('user');

                // Remove cookies.
                delete_cookie();

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