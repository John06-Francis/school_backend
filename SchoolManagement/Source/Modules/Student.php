<?php

namespace Source\Modules;

use Source\Main;
use Source\Modules\ModuleInterface;

class Student extends Main implements ModuleInterface
{
    protected $table;
    protected $accepted_parameters;
    protected $response_column;
    private $dbConnect;
    function __construct($db)
    {
        $this->dbConnect = $db;
        $this->table = 'student_information';
        parent::__construct();
    }

    public function httpGet(array $payload, bool $api = true)
    {
        $data = $this->dbConnect->get($this->table, null, ' id, first_name');
        exit($this->response_message->jsonSuccessResponse($data));
    }

    public function httpPost(array $payload)
    {

        echo 'this is post';
    }

    public function httpPut(int $identity, array $payload)
    {
        echo 'this is put';
    }

    public function httpDel($identity, array $payload)
    {
        echo 'this is delete';
    }

    public function httpFileUpload(int $identity, array $payload)
    {
        return $this->Messages->jsonErrorRequestMethodNotServed();
    }
}
