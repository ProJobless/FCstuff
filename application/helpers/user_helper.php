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

/* End of file user_helper.php */
/* File location : ./application/helpers/user_helper.php */