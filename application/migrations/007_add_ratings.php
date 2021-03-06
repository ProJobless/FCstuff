<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations for 'ratings' table.
 *
 * @package    FCstuff
 * @category   Database
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migration_Add_ratings extends CI_Migration {

    /**
     * Create 'ratings' table.
     *
     * @access  public
     */
    public function up()
    {
        $this->dbforge->add_field(array(
            "rating_id INT(10) NOT NULL AUTO_INCREMENT",
            "post_id INT(10)",
            "user_id INT(10)",
            "rating INT(10)",
            "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ));

        $this->dbforge->add_key('rating_id', TRUE);
        $this->dbforge->add_key('post_id');

        $this->dbforge->create_table('ratings');
    }

    // --------------------------------------------------------------------

    /**
     * Drop 'ratings' table.
     *
     * @access  public
     */
    public function down()
    {
        $this->dbforge->drop_table('ratings');
    }
}

/* End of file 007_add_ratings.php */
/* File location : ./application/migrations/007_add_ratings.php */