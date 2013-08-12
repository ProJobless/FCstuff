<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'logs' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_logs extends CI_Migration {

    /**
     * Populate 'logs' table.
     *
     * @access  public
     */
    public function up()
    {
        $ip_address = $this->session->userdata('ip_address');
        $user_agent = $this->session->userdata('user_agent');

        for ($i = 0; $i < 5 ; $i++)
        {
            // User
            for ($j = 2; $j <= 6 ; $j++)
            {
                $this->db->insert('logs', array(
                    'user_id'    => $j,
                    'controller' => 'Example Controller',
                    'method'     => 'Example Method',
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent
                ));
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'logs' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('logs');
    }
}

/* End of file 016_populate_logs.php */
/* File location : ./application/migrations/016_populate_logs.php */