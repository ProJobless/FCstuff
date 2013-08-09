<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'posts' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_posts extends CI_Migration {

    /**
     * Create 'posts' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "post_id INT(10) NOT NULL AUTO_INCREMENT",
            "user_id INT(10)",
            "content TEXT",
            "image VARCHAR(100)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "modified BOOLEAN DEFAULT FALSE",
            "last_modified_timestamp TIMESTAMP",
            "last_comment_timestamp TIMESTAMP",
            "rating_score INT(10)",
            "rating_count INT(10)"
        ));

        $this->dbforge->add_key('post_id', TRUE);
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('posts');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'posts' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('posts');
    }
}

/* End of file 004_add_posts.php */
/* File location : ./application/migrations/004_add_posts.php */