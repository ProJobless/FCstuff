<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'friends' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_friends extends CI_Migration {

    /**
     * Create 'friends' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "user_id INT(10)",
            "friend_id INT(10)",
            "status VARCHAR(10)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ));

        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('friend_id');

        $this->dbforge->create_table('friends');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'friends' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('friends');
    }
}

/* End of file 003_add_friends.php */
/* File location : ./application/migrations/003_add_friends.php */