<?php

namespace SchoolManagement\Modules;

use MysqliDb;
use SchoolManagement\Main;

class Student extends Main
{
    protected $table;
    protected $accepted_parameters;
    protected $response_column;

    function __construct()
    {
        $this->table = 'student_info';
        $this->initDbConnection();
    }
}
