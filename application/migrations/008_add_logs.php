<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'logs' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_logs extends CI_Migration {

    /**
     * Create 'logs' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "log_id INT(10) NOT NULL AUTO_INCREMENT",
            "user_id INT(10)",
            "controller VARCHAR(30)",
            "method VARCHAR(30)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "ip_address VARCHAR(15)",
            "user_agent VARCHAR(300)"
        ));

        $this->dbforge->add_key('log_id', TRUE);
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('logs');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'logs' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('logs');
    }
}

/* End of file 008_add_logs.php */
/* File location : ./application/migrations/008_add_logs.php */