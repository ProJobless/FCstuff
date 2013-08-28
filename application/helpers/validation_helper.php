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
    function is_valid($type = '', $content = '')
    {
        $CI = get_instance();

        $CI->load->model('user');

        switch ($type)
        {
            case 'id':

                if ( ! is_numeric($content))
                {
                    return FALSE;
                }

                if (empty($content))
                {
                    return FALSE;
                }

                if (strlen($content) > 10)
                {
                    return FALSE;
                }

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'username':

                $CI->session->set_flashdata('username_invalid', TRUE);

                if ($CI->user->read($content))
                {
                    return FALSE;
                }

                if (empty($content))
                {
                    return FALSE;
                }

                if (strlen($content) > 30)
                {
                    return FALSE;
                }

                $CI->session->set_flashdata('username_invalid', FALSE);

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'email':

                $CI->session->set_flashdata('email_invalid', TRUE);

                if ($CI->user->read($content))
                {
                    return FALSE;
                }

                if ( ! filter_var($content, FILTER_VALIDATE_EMAIL))
                {
                    return FALSE;
                }

                $CI->session->set_flashdata('email_invalid', FALSE);

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'password':

                $CI->session->set_flashdata('password_invalid', TRUE);

                if (empty($content))
                {
                    return FALSE;
                }

                if (strlen($content) < 4)
                {
                    return FALSE;
                }

                $CI->session->set_flashdata('password_invalid', FALSE);

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'name':

                $CI->session->set_flashdata('name_invalid', TRUE);

                if (empty($content))
                {
                    return FALSE;
                }

                if (str_word_count($content) < 2)
                {
                    return FALSE;
                }

                if (str_word_count($content) > 3)
                {
                    return FALSE;
                }

                if (strlen($content) > 30)
                {
                    return FALSE;
                }

                $CI->session->set_flashdata('name_invalid', FALSE);

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'md5':

                if (empty($content))
                {
                    return FALSE;
                }

                if (strlen($content) > 32)
                {
                    return FALSE;
                }

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'birthday':

                $CI->session->set_flashdata('birthday_invalid', TRUE);

                $birthday = explode('-', $content);

                if ( ! (count($birthday) == 3))
                {
                    return FALSE;
                }

                $day   = $birthday[0];
                $month = $birthday[1];
                $year  = $birthday[2];

                if ( ! checkdate($month, $day, $year))
                {
                    return FALSE;
                }

                if ( ! date("Y") - $year < 100)
                {
                    return FALSE;
                }

                $CI->session->set_flashdata('birthday_invalid', FALSE);

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'about_me':

                $CI->session->set_flashdata('about_me_invalid', TRUE);

                if (empty($content))
                {
                    return FALSE;
                }

                if (strlen($content) > 500)
                {
                    return FALSE;
                }

                $CI->session->set_flashdata('about_me_invalid', FALSE);

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'gender':

                if ( ! ($content == 'm' OR $content == 'f'))
                {
                    $CI->session->set_flashdata('gender_invalid', TRUE);
                    return FALSE;
                }

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'captcha':

                if ($content != $CI->session->userdata('captcha'))
                {
                    $CI->session->set_flashdata('captcha_invalid', TRUE);
                    return FALSE;
                }

                return TRUE;
                break;

            // ------------------------------------------------------------

            case 'post':
            case 'comment':

                $CI->session->set_flashdata($type . '_invalid', TRUE);

                if (empty($content))
                {
                    return FALSE;
                }

                if (strlen($content) > 1000)
                {
                    return FALSE;
                }

                $CI->session->set_flashdata($type . '_invalid', FALSE);

                return TRUE;
                break;
        }
    }
}

/* End of file validation_helper.php */
/* File location : ./application/helpers/validation_helper.php */