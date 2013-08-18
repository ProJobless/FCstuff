<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Profile Picture Helper
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

if ( ! function_exists('generate_profile_picture'))
{
    /**
     * Generate the default profile picture with a random background color.
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

/* End of file profile_picture_helper.php */
/* File location : ./application/helpers/profile_picture_helper.php */