<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CONVERSATIONS CONTROLLER
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Conversations extends CI_Controller {

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
        $this->load->model('conversation');
        $this->load->model('notification');

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for sending a message.
     *
     * @access   public
     * @param    string
     */
    public function send($ajax = FALSE)
    {
        log_access('conversations', 'send');

        $friend_user_id = $this->input->post('friend_user_id');
        $message        = $this->input->post('message');

        if ($this->_send($friend_user_id, $message))
        {
            $response['success'] = TRUE;
        }

        else
        {
            $response['success'] = FALSE;
        }

        if ( ! $ajax)
        {
            proceed('/');
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * @access   private
     * @param    int       Friend's user id
     * @param    string    Message
     * @return   bool
     */
    private function _send($friend_user_id, $message)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        $user_id = $user['user_id'];

        if ($user['type'] == 'banned')
        {
            return FALSE;
        }

        if ( ! ($friend = $this->user->read($friend_user_id)))
        {
            return FALSE;
        }

        if ( ! ($relationship = $this->friend->read($user_id, $friend_user_id))) 
        {
            return FALSE;
        }

        if ( ! ($relationship[0]['status'] == 'friends'))
        {
            return FALSE;
        }

        if ( ! is_valid('message', $message))
        {
            return FALSE;
        }

        $this->conversation->create(array(
            'user_id'   => $user_id,
            'friend_id' => $friend_user_id,
            'type'      => 'sent',
            'message'   => $message
        ));

        $this->conversation->create(array(
            'user_id'   => $friend_user_id,
            'friend_id' => $user_id,
            'type'      => 'received',
            'message'   => $message
        ));

        $friend_last_seen = strtotime($friend[0]['last_seen']);
        $current_time     = strtotime(gmdate('Y-m-d H:i:s'));
        $diff             = $current_time - $friend_last_seen;

        if ($diff > 40)
        {
            $this->notification->create(array(
                'user_id'  => $friend_user_id,
                'content'  => substr($user['name'] . ' sent you a new message : ' . $message, 0, 500),
                'image'    => $user['user_id'] . '/' . $user['profile_picture'],
                'link'     => '/#conversations-' . $user_id
            ));
        }

        return TRUE;
    }
}

/* End of file conversations.php */
/* File location : ./application/controllers/conversations.php */