<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'notifications' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_notifications extends CI_Migration {

    /**
     * Create 'notifications' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "notification_id INT(10) NOT NULL AUTO_INCREMENT",
            "user_id INT(10)",
            "content VARCHAR(500)",
            "image VARCHAR(32)",
            "link VARCHAR(300)",
            "category VARCHAR(50)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "seen BOOLEAN DEFAULT FALSE"
        ));

        $this->dbforge->add_key('notification_id', TRUE);
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('notifications');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'notifications' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('notifications');
    }
}

/* End of file 019_add_notifications.php */
/* File location : ./application/migrations/019_add_notifications.php */