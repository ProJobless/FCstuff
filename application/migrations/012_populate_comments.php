<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for populating 'comments' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
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
                    $content = "This is a comment by user $k on post $i.";
                    $this->db->insert('comments', array(
                        'post_id' => $i,
                        'user_id' => $k,
                        'content' => $content
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