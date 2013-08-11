<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'friends' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_friends extends CI_Migration {

    /**
     * Populate 'friends' table.
     *
     * @access  public
     */
    public function up()
    {
        $staus = array('friends', 'req_sent', 'req_recvd');

        for ($i = 0; $i < 3; $i++)
        {
            $this->db->insert('friends', array(
                'user_id'   => 2,
                'friend_id' => $i + 3,
                'status'    => $staus[$i]
            ));
        }

        $this->db->insert('friends', array(
            'user_id'   => 3,
            'friend_id' => 2,
            'status'    => 'friends'
        ));

        $this->db->insert('friends', array(
            'user_id'   => 4,
            'friend_id' => 2,
            'status'    => 'req_recvd'
        ));

        $this->db->insert('friends', array(
            'user_id'   => 5,
            'friend_id' => 2,
            'status'    => 'req_sent'
        ));
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'friends' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('friends');
    }
}

/* End of file 010_populate_friends.php */
/* File location : ./application/migrations/010_populate_friends.php */