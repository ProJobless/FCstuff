<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RATINGS CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Ratings extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->model('post');
        $this->load->model('ratings');

        authenticate_cookie();
        try_to_unban();
    }
}

/* End of file ratings.php */
/* File location : ./application/controllers/ratings.php */