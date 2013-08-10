<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'cookies' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_cookies extends CI_Migration {

    /**
     * Populate 'cookies' table.
     *
     * @access  public
     */
    public function up()
    {
        $ip_address = $this->session->userdata('ip_address');
        $user_agent = $this->session->userdata('user_agent');

        // Users
        for ($i = 2; $i <= 6 ; $i++)
        {
            //Cookies
            for ($j = 0; $j < 3; $j++)
            {
                $token = md5(mt_rand());
                $this->db->insert('cookies', array(
                    'user_id'    => $i,
                    'token'      => $token,
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent
                ));
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'cookies' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('cookies');
    }
}

/* End of file 013_populate_cookies.php */
/* File location : ./application/migrations/013_populate_cookies.php */