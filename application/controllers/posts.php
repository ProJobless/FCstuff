<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POSTS CONTROLLER
 *
 * @package    FCstuff
 * @category   Content
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Posts extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('post');
    }
}

/* End of file posts.php */
/* File location : ./application/controllers/posts.php */