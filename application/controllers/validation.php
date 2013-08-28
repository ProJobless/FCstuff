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
     * Validate stuff.
     *
     * @access   public
     * @param    string
     */
    public function check()
    {
        log_access('validation', 'check');

        // Get things from $_POST.
        $content_type = $this->input->post('content_type');
        $content      = $this->input->post('content');

        // Validate it.
        if (is_valid($content_type, $content))
        {
            $response['success'] = TRUE;
            $this->output->set_output(TRUE);
        }

        else
        {
            $response['success'] = FALSE;
        }

        // Return a JSON array.
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }
}
/* End of file validation.php */
/* File location : ./application/controllers/validation.php */