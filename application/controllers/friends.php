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
        $this->load->model('notification');

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

        $this->notification->create(array(
            'user_id'  => $friend_user_id,
            'content'  => $user['name'] . ' wants to be your friend.',
            'category' => 'friends'
        ));

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for accepting friend requests.
     *
     * @access   public
     * @param    string
     */
    public function accept($ajax = FALSE)
    {
        log_access('friends', 'accept');

        $friend_user_id = $this->input->post('friend_user_id');

        if ($this->_accept($friend_user_id))
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
     * Accept a friend request.
     *
     * @access   private
     * @param    int       Friend user id
     * @return   bool
     */
    private function _accept($friend_user_id)
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

        if ( ! isset($relationship[0]))
        {
            return FALSE;
        }

        if ( ! ($relationship[0]['status'] == 'req_received'))
        {
            return FALSE;
        }

        $this->friend->update($user['user_id'], $friend_user_id, array(
            'status' => 'friends'
        ));

        $this->friend->update($friend_user_id, $user['user_id'], array(
            'status' => 'friends'
        ));

        $this->notification->create(array(
            'user_id'  => $friend_user_id,
            'content'  => 'You are now friends with ' . $user['name'] . '.',
            'link'     => 'people/' . $user['username'],
            'category' => 'friends'
        ));

        $this->notification->create(array(
            'user_id'  => $user['user_id'],
            'content'  => 'You are now friends with ' . $friend[0]['name'] . '.',
            'link'     => 'people/' . $friend[0]['username'],
            'category' => 'friends'
        ));

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for deleting a friend.
     *
     * @access   public
     * @param    string
     */
    public function delete($ajax = FALSE)
    {
        log_access('friends', 'delete');

        $friend_user_id = $this->input->post('friend_user_id');

        if ($this->_delete($friend_user_id))
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
     * Delete a friend.
     *
     * @access   private
     * @param    int       Friend user id
     * @return   bool
     */
    private function _delete($friend_user_id)
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

        if ( ! isset($relationship[0]))
        {
            return FALSE;
        }

        $this->friend->delete($user['user_id'], $friend_user_id);

        $this->friend->delete($friend_user_id, $user['user_id']);

        return TRUE;
    }
}

/* End of file friends.php */
/* File location : ./application/controllers/friends.php */