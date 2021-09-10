<?php

namespace Source\Helper;

class InfoMessage
{
    public function errorMessage($errorMessage)
    {
        return json_encode([
            'status' => 'fail',
            'message' => $errorMessage
        ]);
    }

    public function jsonSuccessResponse($data)
    {
        return json_encode([
            'status' => 'success',
            'data' => $data,
            'method' => $_SERVER["REQUEST_METHOD"]
        ], JSON_NUMERIC_CHECK);
    }
}
