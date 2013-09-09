<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NOTIFICATIONS CONTROLLER
 *
 * @package    FCstuff
 * @category   User
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Notifications extends CI_Controller {

    /**
     * Constructor.
     *
     * @access   public
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user');
        $this->load->model('notification');

        authenticate_cookie();
        try_to_unban();
    }

    // --------------------------------------------------------------------

    /**
     * Handle AJAX requests for displaying notifications.
     *
     * @access   public
     */
    public function read()
    {
        log_access('notifications', 'read');

        $before = $this->input->post('before');
        $after = $this->input->post('after');

        $response = $this->_read($before, $after);

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response));
    }

    /**
     * Return notifications for a user.
     *
     * @access   private
     * @param    int
     * @param    int
     */
    private function _read($before = FALSE, $after = FALSE)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            $response['success'] = FALSE;
            return $response;
        }

        $notifications = $this->notification->user($user['user_id'], $before, $after);

        if (count($notifications) < 1)
        {
            $response['success'] = FALSE;
            return $response;
        }

        $response['success'] = TRUE;
        $response['notifications'] = $notifications;

        return $response;
    }

    // --------------------------------------------------------------------

    /**
     * Handle requests for deleting notifications.
     *
     * @access   public
     * @param    string
     */
    public function delete($ajax = FALSE)
    {
        log_access('notifications', 'delete');

        $notification_id = $this->input->post('notification_id');

        if ($this->_delete($notification_id))
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
     * Delete a notification.
     *
     * @access   private
     * @param    int       Notification id
     * @return   bool
     */
    private function _delete($id)
    {
        if ( ! ($user = $this->session->userdata('user')))
        {
            return FALSE;
        }

        if ( ! ($notification = $this->notification->read($id)))
        {
            return FALSE;
        }

        if ( ! ($notification[0]['user_id'] == $user['user_id']))
        {
            return FALSE;
        }

        $this->notification->delete($id);

        return TRUE;
    }
}

/* End of file notifications.php */
/* File location : ./application/controllers/notifications.php */