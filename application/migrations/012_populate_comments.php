<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'comments' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Populate_comments extends CI_Migration {

    /**
     * Populate 'comments' table.
     *
     * @access  public
     */
    public function up()
    {
        // Posts
        for ($i = 1; $i <= 15; $i++)
        {
            // Comments
            for ($j = 1; $j <= 2 ; $j++)
            {
                // Users
                for ($k = 2; $k <= 6; $k++)
                {
                    $content = "This is comment $j by user $k on post $i.";
                    $this->db->insert('comments', array(
                        'post_id'   => $j,
                        'user_id'   => $k,
                        'content'   => $content,
                        'rating_score' => rand(200, 500),
                        'rating_count' => rand(0, 100)
                    ));
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty 'comments' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->db->empty_table('comments');
    }
}

/* End of file 012_populate_comments.php */
/* File location : ./application/migrations/012_populate_comments.php */