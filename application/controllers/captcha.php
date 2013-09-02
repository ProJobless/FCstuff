<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CAPTCHA CONTROLLER
 *
 * @package    FCstuff
 * @category   Security
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Captcha extends CI_Controller {

    /**
     * Output captcha image and set the captcha string as a session variable.
     *
     * @access  public
     */
    public function index()
    {
        log_access('captcha', 'generate');

        // Generate captcha text.
        $this->load->helper('string');
        $text = strtolower(random_string('alpha', 5));
        $this->session->set_userdata('captcha', $text);

        // Create image.
        $image = imagecreatetruecolor(125, 60);
        imagesavealpha($image, true);

        // Set image background.
        $back = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $back);

        // Add captcha text.
        $font = 'assets/fonts/captcha.ttf';
        $color = imagecolorallocate($image, 217, 4, 43);
        imagettftext($image, 40, 0, 5, 40, $color, $font, $text);

        // Output image.
        $this->output->set_content_type('image/png');
        $this->output->set_output(imagepng($image));
        imagedestroy($image);
    }
}

/* End of file captcha.php */
/* File location : ./application/controllers/captcha.php */