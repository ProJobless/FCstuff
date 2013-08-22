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
            case 'user_id':

                if (strlen($content) > 0
                 && strlen($content) <= 10
                 && is_numeric($content)
                 && $CI->user->read($content))
                {
                    return TRUE;
                }

                break;

            // ------------------------------------------------------------

            case 'username':

                if ( ! $CI->user->read($content)
                 && strlen($content) > 0
                 && strlen($content) <= 30)
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_username', TRUE);
                }

                break;

            // ------------------------------------------------------------

            case 'email':

                if ( ! $CI->user->read($content)
                  && filter_var($content, FILTER_VALIDATE_EMAIL))
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_email', TRUE);
                }

                break;

            // ------------------------------------------------------------

            case 'password':

                if (strlen($content) > 0)
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_password', TRUE);
                }

                break;

            // ------------------------------------------------------------

            case 'name':

                if (strlen($content) > 0
                 && strlen($content) < 31
                 && str_word_count($content) > 1
                 && str_word_count($content) < 4)
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_name', TRUE);
                }

                break;

            // ------------------------------------------------------------

            case 'md5':
            case 'verification_key':
            case 'recovery_key':
            case 'unsubscription_key':

                if (strlen($content) > 0 && strlen($content) <= 32)
                {
                    return TRUE;
                }

                break;

            // ------------------------------------------------------------

            case 'birthday':

                $birthday = explode('-', $content);

                if (count($birthday) == 3)
                {
                    $day   = $birthday[0];
                    $month = $birthday[1];
                    $year  = $birthday[2];

                    if (checkdate($month, $day, $year)
                     && date("Y") - $year < 100)
                    {
                        return TRUE;
                    }

                    else
                    {
                        $CI->session->set_flashdata('invalid_birthday', TRUE);
                    }
                }

                else
                {
                    $CI->session->set_flashdata('invalid_birthday', TRUE);
                }

                break;

            // ------------------------------------------------------------

            case 'about_me':

                if (strlen($content) > 0 && strlen($content) <= 500)
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_about_me', TRUE);
                }

                break;

            // ------------------------------------------------------------

            case 'captcha':

                if ($content == $CI->session->userdata('captcha'))
                {
                    return TRUE;
                }

                else
                {
                    $CI->session->set_flashdata('invalid_captcha', TRUE);
                }

                break;
        }
    }
}

/* End of file validation_helper.php */
/* File location : ./application/helpers/validation_helper.php */