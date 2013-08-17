<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'notifications' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_notifications extends CI_Migration {

    /**
     * Populate 'notifications' table.
     *
     * @access  public
     */
    public function up()
    {
        for ($i = 1; $i <= 10; $i++) {
            // User_id
            for ($j = 2; $j < 5; $j++) {
                $notification = array(
                    'user_id'  => $j,
                    'content'  => "This is sample notification $i for user $j.",
                    'link'     => '',
                    'category' => ''
                );
                $this->db->insert('notifications', $notification);
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'notifications' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('notifications');
    }
}

/* End of file 020_populate_notifications.php */
/* File location : ./application/migrations/020_populate_notifications.php */