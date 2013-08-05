<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CAPTCHA CONTROLLER
 *
 * @package    FCstuff
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Captcha extends CI_Controller {

    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();

        // Load string helper.
        // Required for generating random string.
        $this->load->helper('string');
    }

    /**
     * Return captcha image and set the captcha string as a session variable.
     *
     * @access  public
     * @return  image/png
     */
    public function index()
    {
        // Generate captcha text.
        $text = strtolower(random_string('alpha', 5));
        $this->session->set_userdata('captcha', $text);

        // Set font path.
        $font = 'application/assets/fonts/captcha.ttf';

        // Create a 125 x 60 image.
        $image = imagecreatetruecolor(125, 60);
        imagesavealpha($image, true);

        // Set font color.
        $color = imagecolorallocate($image, 39, 97, 155);

        // Set image background.
        $back = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $back);

        // Add captcha text.
        imagettftext($image, 40, 0, 5, 40, $color, $font, $text);

        // Return captcha.
        header("Content-type: image/png");
        imagepng($image);

        // Free up memory.
        imagedestroy($image);
    }
}

/* End of file captcha.php */
/* File location : ./application/controllers/captcha.php */