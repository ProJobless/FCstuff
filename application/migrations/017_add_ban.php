<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'ban' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_ban extends CI_Migration {

    /**
     * Create 'ban' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "ban_id INT(10) NOT NULL AUTO_INCREMENT",
            "user_id INT(10)",
            "offence TEXT",
            "initiator VARCHAR(30)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "expire TIMESTAMP"
        ));

        $this->dbforge->add_key('ban_id', TRUE);
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('ban');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'ban' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('ban');
    }
}

/* End of file 017_add_ban.php */
/* File location : ./application/migrations/017_add_ban.php */