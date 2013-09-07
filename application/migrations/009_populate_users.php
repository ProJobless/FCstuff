<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'users' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
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
        $this->load->helper('user');

        // -------------------------
        // Common values
        // -------------------------

        $data['recovery_key']       = md5(rand());
        $data['verification_key']   = md5(rand());
        $data['unsubscription_key'] = md5(rand());
        $data['birthday']           = date('Y-m-d H:i:s', strtotime("-18 year"));
        $data['last_seen']          = date('Y-m-d H:i:s');

        // -------------------------
        // Create admin user
        // -------------------------

        if ( ! file_exists('user-content/1'))
        {
            mkdir('user-content/1', 0777, TRUE);
        }

        $data['username']         = 'admin';
        $data['email']            = 'admin@localhost';
        $data['password']         = $this->phpass->hash('admin');
        $data['name']             = 'Awesome Admin';
        $data['reputation']       = rand(0, 1000);
        $data['posts']            = rand(0, 1000);
        $data['friends']          = rand(0, 500);
        $data['about_me']         = 'I am the awesome admin!';
        $data['type']             = 'admin';
        $data['profile_picture']  = generate_profile_picture(1);
        $data['verified']         = TRUE;

        $this->db->insert('users', $data);

        unset($data['type']);

        // -------------------------
        // Create 5 normal users
        // -------------------------

        $data['password'] = $this->phpass->hash('pass');

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
            $data['posts']           = rand(0, 1000);
            $data['about_me']        = 'Hello! I am user' . $i . '!';
            $data['profile_picture'] = generate_profile_picture($i);
            $data['verified']        = FALSE;

            $this->db->insert('users', $data);
        }

        // -------------------------
        // Unsubscribe User 3
        // -------------------------

        $this->db->where('user_id', 3);
        $this->db->set('unsubscribed', TRUE);
        $this->db->update('users');

        // -------------------------
        // Verify User 4
        // -------------------------

        $this->db->where('user_id', 4);
        $this->db->set('verified', TRUE);
        $this->db->set('verification_key', md5(rand()));
        $this->db->update('users');

        // -------------------------
        // Ban User 5
        // -------------------------

        $this->db->where('user_id', 5);
        $this->db->set('type', 'banned');
        $this->db->update('users');

        // -------------------------
        // Delete User 6
        // -------------------------

        $this->db->where('user_id', 6);
        $this->db->set(array(
            'password'           => NULL,
            'reputation'         => 0,
            'posts'              => 0,
            'friends'            => 0,
            'type'               => 'deleted',
            'verification_key'   => NULL,
            'recovery_key'       => NULL,
            'unsubscription_key' => NULL,
            'birthday'           => NULL,
            'about_me'           => NULL,
            'profile_picture'    => NULL
        ));
        $this->db->update('users');

        $this->load->helper('file');

        delete_files('user-content/6/');

    }

    // --------------------------------------------------------------------

    /**
     * Rollback changes.
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