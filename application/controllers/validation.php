<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VALIDATION CONTROLLER
 *
 * @package    FCstuff
 * @category   Validation
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Validation extends CI_Controller {

    /**
     * Output TRUE if the provided content is valid.
     *
     * @access   public
     * @param    string
     */
    public function check($type = '')
    {
        log_access('validation', 'check');

        // Get the content to validate from $_POST.
        $content = $this->input->post('content');

        // Validate it.
        if (is_valid($type, $content))
        {
            $this->output->set_output(TRUE);
        }
    }
}
/* End of file validation.php */
/* File location : ./application/controllers/validation.php */