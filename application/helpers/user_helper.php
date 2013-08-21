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

/* End of file user_helper.php */
/* File location : ./application/helpers/user_helper.php */