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

    // --------------------------------------------------------------------

    /**
     * Handle requests for sending friend requests.
     *
     * @access   public
     * @param    string
     */
    public function send($ajax = FALSE)
    {
        log_access('friends', 'send');

        $friend_user_id = $this->input->post('friend_user_id');

        if ($this->_send($friend_user_id))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            proceed('people/' . $friend_user_id);
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Send a friend request.
     *
     * @access   private
     * @param    int       Friend's user id
     * @return   bool
     */
    private function _send($friend_user_id)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ($user['type'] == 'banned')
        {
            return FALSE;
        }

        if ( ! ($friend = $this->user->read($friend_user_id)))
        {
            return FALSE;
        }

        $relationship = $this->friend->read($user['user_id'], $friend_user_id);

        if (isset($relationship[0]))
        {
            return FALSE;
        }

        $this->friend->create(array(
            'user_id'   => $user['user_id'],
            'friend_id' => $friend_user_id,
            'status'    => 'req_sent'
        ));

        $this->friend->create(array(
            'user_id'   => $friend_user_id,
            'friend_id' => $user['user_id'],
            'status'    => 'req_received'
        ));

        return TRUE;
    }
}

/* End of file friends.php */
/* File location : ./application/controllers/friends.php */