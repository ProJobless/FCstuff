<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'users' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_users extends CI_Migration {

    /**
     * Create 'users' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "user_id INT(10) NOT NULL AUTO_INCREMENT",
            "username VARCHAR(30)",
            "email VARCHAR(50)",
            "password VARCHAR(200)",
            "name VARCHAR(30)",
            "reputation INT(10) DEFAULT '0' ",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "type VARCHAR(20) DEFAULT 'standard' ",
            "verified BOOLEAN DEFAULT FALSE",
            "verification_key VARCHAR(32)",
            "recovery_key VARCHAR(32)",
            "birthday DATE",
            "about VARCHAR(500)",
            "gender VARCHAR(1)"
        ));

        $this->dbforge->add_key('user_id', TRUE);
        $this->dbforge->add_key('username');

        $this->dbforge->create_table('users');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'users' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}

/* End of file 001_add_users.php */
/* File location : ./application/migrations/001_add_users.php */