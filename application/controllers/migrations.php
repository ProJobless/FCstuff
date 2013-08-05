<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MIGRATIONS CONTROLLER
 *
 * @package    FCstuff
 * @author     Abhijit Parida
 * @copyright  Copyright (c) 2013, FCstuff
 */
class Migrations extends CI_Controller {

    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();

        // Load the migration library.
        $this->load->library('migration');
    }

    // --------------------------------------------------------------------

    /**
     * Migrate to the latest version.
     *
     * @access  public
     * @return  void
     */
    public function index()
    {
        // Migrate to latest version.
        if ($this->migration->latest())
        {
            echo "Migrations successful.";    
        }

        // Else, show errors.
        else
        {
            show_error($this->migration->error_string());
        }
    }

    // --------------------------------------------------------------------

    /**
     * Rollback all migrations.
     *
     * @access  public
     * @return  void
     */
    public function rollback()
    {
        $this->migration->version(0);
        echo "Rollback successful.";
    }
}

/* End of file migrations.php */
/* File location : ./application/controllers/migrations.php */