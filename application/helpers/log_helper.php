<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Log Helper
 *
 * @package    FCstuff
 * @category   Log
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

if ( ! function_exists('log_access'))
{
    /**
     * Log the controller and method accessed by the user.
     *
     * @access   public
     * @param    string   Controller name
     * @param    string   Method name
     */
    function log_access($controller, $method)
    {
        $CI = get_instance();

        $CI->load->model('log');

        $user = $CI->session->userdata('user');
        $ip_address = $CI->input->ip_address();
        $user_agent = $CI->input->user_agent();

        $CI->log->create(array(
            'user_id'    => $user['user_id'],
            'controller' => $controller,
            'method'     => $method,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent
        ));
    }
}

/* End of file log_helper.php */
/* File location : ./application/helpers/log_helper.php */