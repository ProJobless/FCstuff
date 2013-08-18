<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AUTHENTICATION CONTROLLER
 *
 * @package    FCstuff
 * @category   Authentication
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Authentication extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->helper('log');
        $this->load->helper('cookie');
        $this->load->helper('url');
        $this->load->library('phpass');
    }

    // --------------------------------------------------------------------

    /**
     * Authenticate user.
     *
     * @access   public
     * @param    string
     */
    public function login($ajax = FALSE)
    {
        log_access('authentication', 'login');

        $identifier = $this->input->post('identifier');
        $password   = $this->input->post('password');
        $remember   = $this->input->post('remember');

        if ($identifier && $password && $auth = $this->user->read($identifier))
        {
            if ($this->phpass->check($password, $auth[0]['password']))
            {
                if ($remember)
                {
                    set_cookie($auth[0]['user_id']);
                }

                if ($ajax)
                {
                    echo TRUE;
                }

                else
                {
                    redirect('pages/feed');
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Logout user.
     *
     * @access   public
     */
    public function logout()
    {
        log_access('authentication', 'logout');

        // Unset session array.
        $this->session->unset_userdata('user');

        // Remove cookie.
        delete_cookie();
    }
}

/* End of file authentication.php */
/* File location : ./application/controllers/authentication.php */