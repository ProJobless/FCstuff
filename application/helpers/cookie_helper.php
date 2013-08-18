<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cookie Helper
 *
 * @package    FCstuff
 * @category   Authentication
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

if ( ! function_exists('authenticate_cookie'))
{
    /**
     * Authenticate user via cookie.
     *
     * @access   public
     */
    function authenticate_cookie()
    {
        $CI = get_instance();

        $CI->load->model('cookie');
        $CI->load->model('user');

        $cookie = $CI->input->cookie('token');
        $cookie = explode('$', $cookie);

        if (count($cookie) == 2)
        {
            // Extract user_id and token from the cookie.
            $user_id = $cookie[0];
            $token   = $cookie[1];

            // Check if token is valid.
            $auth = $CI->cookie->read($user_id, $token);

            if ($auth)
            {
                // Set user data as session array.
                $user = $CI->user->read($auth[0]['user_id']);
                $CI->session->set_userdata('user', $user[0]);

                $token = md5(rand());
                $ip_address = $CI->input->ip_address();
                $user_agent = substr($CI->input->user_agent(), 0, 300);
                $cookie = $user_id . '$' . $token;

                // Update token in database.
                $CI->cookie->update($auth[0]['cookie_id'], array(
                    'token'      => $token,
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent
                ));

                // Update token in the cookie.
                $CI->input->set_cookie('token', $cookie, 2592000);
            }

            // Destroy cookie if token is invalid.
            else
            {
                $CI->input->set_cookie('token', '');
            }
        }

        // Destroy the cookie if it is invalid.
        else
        {
            $CI->input->set_cookie('token', '');
        }
    }
}

/* End of file cookie_helper.php */
/* File location : ./application/helpers/cookie_helper.php */