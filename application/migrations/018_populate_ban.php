<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'ban' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_ban extends CI_Migration {

    /**
     * Populate 'ban' table.
     *
     * @access  public
     */
    public function up()
    {
        $data = array(
            'user_id'   => 5,
            'offence'   => 'Sample banning.',
            'initiator' => 'Admin'
        );
        $this->db->set('expire', 'NOW() + INTERVAL 1 HOUR', FALSE);
        $this->db->insert('ban', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'ban' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('ban');
    }
}

/* End of file 018_populate_ban.php */
/* File location : ./application/migrations/018_populate_ban.php */