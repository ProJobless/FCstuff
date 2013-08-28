<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'posts' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_posts extends CI_Migration {

    /**
     * Populate 'posts' table.
     *
     * @access  public
     */
    public function up()
    {
        // Posts
        for ($i = 1; $i <= 10; $i++)
        {
            // Users
            for ($j = 2; $j <= 6; $j++)
            {
                $content = "This is post $i made by user $j.";
                $this->db->insert('posts', array(
                    'user_id'   => $j,
                    'content'   => $content,
                    'rating_score' => rand(200, 500),
                    'rating_count' => rand(0, 100)
                ));
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'posts' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('posts');
    }
}

/* End of file 011_populate_posts.php */
/* File location : ./application/migrations/011_populate_posts.php */