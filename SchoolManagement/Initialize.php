<?php

require_once './vendor/autoload.php';

use Source\Main;

try {
    $Main = new Main();
    $response = $Main->requestMethod();
    exit($response);
} catch (Exception $e) {
    header('Content-Type: application/json');
    exit(json_encode([
        'message' => 'Internal Server Error : Something went wrong'
    ]));
}
