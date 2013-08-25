<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * URL Helper
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

if ( ! function_exists('proceed'))
{
    /**
     * Proceed to the URL provided in $_GET['continue']. If $_GET['continue']
     * is not set, then proceed to URL passed to this function.
     *
     * @access   public
     * @param    string
     */
    function proceed($url = FALSE)
    {
        $CI = get_instance();

        if ($continue = $CI->input->get('continue'))
        {
            redirect($continue);
        }

        elseif ($url)
        {
            redirect($url);
        }
    }
}

/* End of file MY_url_helper.php */
/* File location : ./application/helpers/MY_url_helper.php */