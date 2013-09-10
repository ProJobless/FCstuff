<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'comments' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_comments extends CI_Migration {

    /**
     * Create 'comments' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "comment_id INT(10) NOT NULL AUTO_INCREMENT",
            "post_id INT(10)",
            "user_id INT(10)",
            "content MEDIUMTEXT",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "modified BOOLEAN DEFAULT FALSE",
            "last_modified_timestamp TIMESTAMP",
        ));

        $this->dbforge->add_key('comment_id', TRUE);
        $this->dbforge->add_key('post_id');
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('comments');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'comments' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('comments');
    }
}

/* End of file 005_add_comments.php */
/* File location : ./application/migrations/005_add_comments.php */