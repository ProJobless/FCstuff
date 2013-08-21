<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Validation Helper
 *
 * @package    FCstuff
 * @category   Validation
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

if ( ! function_exists('is_valid'))
{
    /**
     * Check if the provided content is valid.
     *
     * @access   public
     * @param    string   Type of content
     * @param    string   Content
     * @return   boolean  TRUE if content is valid
     */
    function is_valid($type, $content)
    {
        $CI = get_instance();

        $CI->load->model('user');

        switch ($type)
        {
            case 'name':

                if (strlen($content) > 0 &&
                    strlen($content) < 31 &&
                    str_word_count($content) > 1 &&
                    str_word_count($content) < 4)
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_name', TRUE);
                    return FALSE;
                }

                break;

            // -----------------------------------------------------------

            case 'email':

                if ( ! $CI->user->read($content) &&
                    filter_var($content, FILTER_VALIDATE_EMAIL))
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_email', TRUE);
                    return FALSE;
                }

                break;

            // -----------------------------------------------------------

            case 'password':

                if (strlen($content) > 0)
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_email', TRUE);
                    return FALSE;
                }

                break;

            // -----------------------------------------------------------

            case 'captcha':

                if ($content == $CI->session->userdata('captcha'))
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_captcha', TRUE);
                    return FALSE;
                }

                break;

            // -----------------------------------------------------------

            case 'user_id':

                if (strlen($content) > 0
                    && strlen($content) <= 10
                    && is_numeric($content)
                    && $CI->user->read($content))
                {
                    return TRUE;
                }

                break;
        }
    }
}

/* End of file validation_helper.php */
/* File location : ./application/helpers/validation_helper.php */