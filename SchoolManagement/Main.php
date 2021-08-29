<?php

namespace SchoolManagement;

require_once './vendor/autoload.php';
require_once(__DIR__ . '/Modules/Student.php');

use MysqliDb;
use SchoolManagement\Modules\Student as ModulesStudent;


class Main
{
    public $db;
    function __construct()
    {
        $this->Student = new ModulesStudent;
    }

    /**
     * Initialize database connection
     */
    public function initDbConnection()
    {
        // Database Configuration
        $this->db = new MysqliDb(array(
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'db' => 'school_management',
            'charset' => 'utf8'
        ));
    }
}

$main = new Main();
