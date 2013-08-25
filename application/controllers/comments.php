<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * COMMENTS CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Comments extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('post');
        $this->load->model('comments');

        authenticate_cookie();
        update_session_array();
        try_to_unban();
    }
}

/* End of file comments.php */
/* File location : ./application/controllers/comments.php */