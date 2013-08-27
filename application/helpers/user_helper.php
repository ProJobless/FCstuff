<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User Helper
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

if ( ! function_exists('generate_username'))
{
    /**
     * Generate a unique username based on the user's email address.
     *
     * @access   public
     * @param    string   Email address
     * @return   string   Username
     */
    function generate_username($email)
    {
        $CI = get_instance();

        $CI->load->model('user');

        // Extract the username from the email address.
        $email = explode('@', $email);
        $username = $email[0];

        // Assume that the username is unique.
        $unique = TRUE;

        // Check if the username is actually unique.
        if ($CI->user->read($username))
        {
            $unique = FALSE;
        }

        // Generate a unique username if it isn't unique.
        $i = 1;

        while ( ! $unique)
        {
            $tmp_username = $username . $i;

            if ($CI->user->read($tmp_username))
            {
                $i++;
            }

            else
            {
                $unique = TRUE;
                $username = $tmp_username;
            }
        }

        return $username;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('generate_profile_picture'))
{
    /**
     * Generate a profile picture.
     *
     * @access   public
     * @param    string   User_id
     * @return   string   Filename of the generated image
     */
    function generate_profile_picture($user_id)
    {
        // Generate the profile picture.
        $canvas     = imagecreatetruecolor(320, 320);
        $avatar     = imagecreatefrompng('assets/images/avatar.png');
        $red        = mt_rand(0, 255);
        $green      = mt_rand(0, 255);
        $blue       = mt_rand(0, 255);
        $background = imagecolorallocate($canvas, $red, $green, $blue);

        imagefill($canvas, 0, 0, $background);
        imagecopy($canvas, $avatar, 0, 0, 0, 0, 320, 320);

        // Save the image.
        $filename = md5($user_id . rand());
        $location = 'user-content/' . $user_id . '/' . $filename . '.png';
        imagepng($canvas, $location);

        // Return the filename.
        return $filename;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('update_last_seen_timestamp'))
{
    /**
     * Update the last seen timestamp if a user is logged in.
     *
     * @access   public
     */
    function update_last_seen_timestamp()
    {
        $CI = get_instance();

        $CI->load->model('user');

        // Check if user is logged in.
        if ($user = $CI->session->userdata('user'))
        {
            // Update timestamp.
            $CI->user->update($user['user_id'], array(
                'last_seen' => gmdate('Y-m-d H:i:s')
            ));
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('login'))
{
    /**
     * Login user.
     *
     * @access   public
     * @param    int      User id
     * @param    bool     Flag for setting authentication cookie
     * @return   bool     TRUE if login was successful.
     */
    function login($user_id, $cookie = FALSE)
    {
        $CI = get_instance();

        $CI->load->model('user');

        $user = $CI->user->read($user_id);

        if ($user[0]['type'] != 'deleted')
        {
            // Set user data as session array.
            $CI->session->set_userdata('user', $user[0]);

            // Update the last seen timestamp.
            update_last_seen_timestamp();

            // If banned, to unban the user if the ban has expired.
            try_to_unban();

            // Set an authentication cookie.
            if ($cookie)
            {
                set_cookie($user[0]['user_id']);
            }

            return TRUE;
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('logout'))
{
    /**
     * Logout user.
     *
     * @access   public
     */
    function logout()
    {
        $CI = get_instance();

        // Update the last seen timestamp.
        update_last_seen_timestamp();

        // Remove user array from $_SESSION.
        $CI->session->unset_userdata('user');

        // Remove cookies.
        delete_cookie();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('update_session_array'))
{
    /**
     * Update the user array in $_SESSION.
     *
     * @access   public
     */
    function update_session_array()
    {
        $CI = get_instance();

        // Check if the user is logged in.
        if ($user = $CI->session->userdata('user'))
        {
            // Load User model.
            $CI->load->model('user');

            // Get new data.
            $new_user_data = $CI->user->read($user['user_id']);

            // Update user array.
            $CI->session->set_userdata('user', $new_user_data[0]);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('try_to_unban'))
{
    /**
     * Un-Ban the user if the ban has expired.
     *
     * @access   public
     */
    function try_to_unban()
    {
        $CI = get_instance();

        // Check if the user is logged in.
        if ($user = $CI->session->userdata('user'))
        {
            // Check if the user is banned.
            if ($user['type'] == 'banned')
            {
                // Load Ban model.
                $CI->load->model('ban');

                // Get the ban expiration timestamp and current timestamp.
                $ban = $CI->ban->read($user['user_id']);
                $ban_expire   = strtotime($ban[0]['expire']);
                $current_time = strtotime(gmdate('Y-m-d H:i:s'));

                // Has the ban expired?
                if ($ban_expire < $current_time)
                {
                    $CI->load->model('user');

                    // Update user details in database.
                    $CI->user->update($user['user_id'], array(
                        'type' => 'standard'
                    ));

                    // Update session array.
                    update_session_array();
                }
            }
        }
    }
}

/* End of file user_helper.php */
/* File location : ./application/helpers/user_helper.php */