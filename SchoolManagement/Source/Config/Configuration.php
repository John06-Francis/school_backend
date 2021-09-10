<?php

namespace Source\Config;

class Configuration
{
    protected $conn;
    function __construct()
    {
        // Database Configuration
        $this->conn = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'db' => 'school_managent_system',
            'prefix' => 'tbl_',
            'charset' => 'utf8',
        ];
    }

    public function DatabaseConnection()
    {
        return $this->conn;
    }
}
