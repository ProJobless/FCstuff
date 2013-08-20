<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * USERS CONTROLLER
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Users extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->helper('user');
        $this->load->library('phpass');
    }
}

/* End of file users.php */
/* File location : ./application/controllers/users.php */