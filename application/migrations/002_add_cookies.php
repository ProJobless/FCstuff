<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'cookies' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_cookies extends CI_Migration {

    /**
     * Create 'cookies' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "cookie_id INT(10) NOT NULL AUTO_INCREMENT",
            "user_id INT(10)",
            "token VARCHAR(32)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "ip_address VARCHAR(15)",
            "user_agent VARCHAR(300)"
        ));

        $this->dbforge->add_key('cookie_id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('token');

        $this->dbforge->create_table('cookies');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'cookies' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('cookies');
    }
}

/* End of file 002_add_cookies.php */
/* File location : ./application/migrations/002_add_cookies.php */