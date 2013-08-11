<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'conversations' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_conversations extends CI_Migration {

    /**
     * Populate 'conversations' table.
     *
     * @access  public
     */
    public function up()
    {
        // User
        for ($i = 3; $i <= 6; $i++)
        {
            $message = "This message is sent to user $i by user 2.";
            $this->db->insert('conversations', array(
                'user_id'    => 2,
                'friend_id'  => $i,
                'type'       => 'sent',
                'message'    => $message
            ));

            $message = "This message from user 2 is recieved by user $i.";
            $this->db->insert('conversations', array(
                'user_id'    => $i,
                'friend_id'  => 2,
                'type'       => 'recieved',
                'message'    => $message
            ));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'conversations' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('conversations');
    }
}

/* End of file 014_populate_conversations.php */
/* File location : ./application/migrations/014_populate_conversations.php */