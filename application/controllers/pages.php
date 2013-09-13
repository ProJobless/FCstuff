<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PAGES CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Pages extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Display the landing page.
     *
     * @access   public
     */
    public function landing()
    {
        log_access('pages', 'landing');

        if ( ! $this->session->userdata('user'))
        {
            $this->load->view('landing');
        }
    }
}

/* End of file pages.php */
/* File location : ./application/controllers/pages.php */