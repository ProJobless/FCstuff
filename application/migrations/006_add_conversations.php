<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'conversations' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_conversations extends CI_Migration {

    /**
     * Create 'conversations' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "message_id INT(10) NOT NULL AUTO_INCREMENT",
            "user_id INT(10)",
            "friend_id INT(10)",
            "type VARCHAR(10)",
            "message TEXT",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "seen BOOLEAN DEFAULT FALSE"
        ));

        $this->dbforge->add_key('message_id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('friend_id');

        $this->dbforge->create_table('conversations');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'conversations' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('conversations');
    }
}

/* End of file 006_add_conversations.php */
/* File location : ./application/migrations/006_add_conversations.php */