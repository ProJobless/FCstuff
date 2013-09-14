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

        else
        {
            $this->main();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Display content using the main template.
     *
     * @access   public
     * @param    string
     * @param    string
     */
    public function main($content_type = 'feed', $content_id = FALSE)
    {
        log_access('pages', 'main');

        if ($user = $this->session->userdata('user'))
        {
            unset($user['password']);
            unset($user['timestamp']);
            unset($user['verification_key']);
            unset($user['recovery_key']);
            unset($user['unsubscription_key']);

            $global = array(
                'base_url'     => base_url(),
                'content_type' => $content_type,
                'content_id'   => $content_id
            );

            $this->load->view('main_template', array(
                'title' => 'FCstuff',
                'user'  => $user,
                'json'  => array(
                    'global' => json_encode($global),
                    'user'   => json_encode($user)
                )
            ));
        }

        else
        {
            $this->landing();
        }
    }
}

/* End of file pages.php */
/* File location : ./application/controllers/pages.php */