<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * LOG MODEL
 *
 * @package    FCstuff
 * @category   Logs
 * @author     Abhijit Parida
 * @license    The MIT License (MIT)
 * @copyright  Copyright (c) 2013, FCstuff
 */

class Log extends CI_Model {

    /**
     * Create a new log entry.
     *
     * @access   public
     * @param    array
     */
    public function create($data)
    {
        $this->db->insert('logs', $data);
    }
}

/* End of file log.php */
/* File location : ./application/models/log.php */