<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'users' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_users extends CI_Migration {

    /**
     * Populate 'users' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->load->library('phpass');
        $this->load->helper('profile_picture');

        // -------------------------
        // Common values
        // -------------------------

        $data['verification_key'] = md5(rand());
        $data['recovery_key']     = md5(rand());
        $data['birthday']         = date('Y-m-d H:i:s', strtotime("-18 year"));
        $data['gender']           = 'm';
        $data['last_seen']        = date('Y-m-d H:i:s');
        $data['last_ip_address']  = $this->session->userdata('ip_address');
        $data['last_user_agent']  = $this->session->userdata('user_agent');

        // -------------------------
        // Create admin user
        // -------------------------

        if ( ! file_exists('user-content/1'))
        {
            mkdir('user-content/1', 0777, TRUE);
        }

        $data['username']        = 'admin';
        $data['email']           = 'admin@localhost';
        $data['password']        = $this->phpass->hash('admin');
        $data['name']            = 'Awesome Admin';
        $data['reputation']      = rand(0, 1000);
        $data['about']           = 'I am the awesome admin!';
        $data['type']            = 'admin';
        $data['verified']        = TRUE;
        $data['profile_picture'] = generate_profile_picture(1);

        $this->db->insert('users', $data);

        unset($data['type']);

        // -------------------------
        // Create 5 normal users
        // -------------------------

        $data['password']        = $this->phpass->hash('pass');

        for ($i = 2; $i <= 6; $i++)
        {
            if ( ! file_exists('user-content/' . $i))
            {
                mkdir('user-content/' . $i, 0777, TRUE);
            }

            $data['username']        = 'user' . $i;
            $data['email']           = 'user' . $i . '@localhost';
            $data['name']            = 'Fname' . $i . ' Lname' . $i;
            $data['reputation']      = rand(0, 1000);
            $data['about']           = 'Hello! I am user' . $i . '!';
            $data['profile_picture'] = generate_profile_picture($i);

            $this->db->insert('users', $data);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'users' table and delete user content.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('users');

        $this->load->helper('file');

        for ($i = 1; $i <= 6 ; $i++)
        {
            delete_files('user-content/' . $i . '/');
        }
    }
}

/* End of file 009_populate_users.php */
/* File location : ./application/migrations/009_populate_users.php */