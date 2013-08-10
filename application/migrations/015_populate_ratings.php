<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'ratings' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_ratings extends CI_Migration {

    /**
     * Populate 'ratings' table.
     *
     * @access  public
     */
    public function up()
    {
        // User
        for ($i = 2; $i <= 6; $i++)
        {
            // Posts
            for ($j = 1; $j < 5; $j++)
            {
                $this->db->insert('ratings', array(
                    'content_id' => $j,
                    'type'       => 'post',
                    'user_id'    => $i,
                    'rating'     => rand(0, 5)
                ));
            }

            // Comments
            for ($j = 1; $j < 5; $j++)
            {
                $this->db->insert('ratings', array(
                    'content_id' => $j,
                    'type'       => 'comment',
                    'user_id'    => $i,
                    'rating'     => rand(0, 5)
                ));
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'ratings' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('ratings');
    }
}

/* End of file 015_populate_ratings.php */
/* File location : ./application/migrations/015_populate_ratings.php */