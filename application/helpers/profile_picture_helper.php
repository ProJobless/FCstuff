<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Profile Picture Helper
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */

// ------------------------------------------------------------------------

/**
 * Generate the default profile picture with a random background color.
 *
 * @access   public
 * @param    string   User id
 * @return   string   Filename of the generated image
 */
if ( ! function_exists('generate_profile_picture'))
{
	function generate_profile_picture($user_id)
	{
        // -------------------------
        // Generate image.
        // -------------------------

        $canvas = imagecreatetruecolor(320, 320);
        $avatar = imagecreatefrompng('assets/images/avatar.png');

        $back = imagecolorallocate($canvas, rand(0,255), rand(0,255), rand(0,255));

        imagefill($canvas, 0, 0, $back);

        imagecopy($canvas, $avatar, 0, 0, 0, 0, 320, 320);

        // -------------------------
        // Generate filename.
        // -------------------------

        $filename = md5($user_id . rand());

        // -------------------------
        // Save image.
        // -------------------------

        $location = 'user-content/' . $user_id . '/' . $filename . '.png';

        imagepng($canvas, $location);

        imagedestroy($canvas);
        imagedestroy($avatar);

        // -------------------------
        // Return filename.
        // -------------------------

        return $filename;
	}
}

/* End of file profile_picture_helper.php */
/* File location : ./application/helpers/profile_picture_helper.php */