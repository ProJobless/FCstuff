<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Authentication Helper
 *
 * @package    FCstuff
 * @category   Authentication
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

/**
 * Check authentication cookie and login user.
 *
 * @access   public
 */
if ( ! function_exists('authenticate_cookie'))
{
    function authenticate_cookie()
    {
        $CI = get_instance();

        $CI->load->model('cookie');
        $CI->load->model('user');

        $cookie = $CI->input->cookie('token');
        $cookie = explode('$', $cookie);

        if (count($cookie) == 2)
        {
            $user_id = $cookie[0];
            $token   = $cookie[1];

            $auth = $CI->cookie->read($user_id, $token);

            if ($auth)
            {
                $user = $CI->user->read($auth[0]['user_id']);
                $CI->session->set_userdata('user', $user[0]);

                $token = md5(rand());
                $ip_address = $CI->input->ip_address();
                $user_agent = substr($CI->input->user_agent(), 0, 300);
                $cookie = $user_id . '$' . $token;

                $data = array(
                    'token'      => $token,
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent
                );

                $CI->cookie->update($auth[0]['cookie_id'], $data);

                $CI->input->set_cookie('token', $cookie, 2592000);
            }

            else
            {
                $CI->input->set_cookie('token', '');
            }
        }
    }
}

/* End of file authentication.php */
/* File location : ./application/helpers/authentication.php */