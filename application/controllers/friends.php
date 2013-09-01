<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FRIENDS CONTROLLER
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Friends extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->model('friend');

        authenticate_cookie();
        try_to_unban();
    }
}

/* End of file friends.php */
/* File location : ./application/controllers/friends.php */