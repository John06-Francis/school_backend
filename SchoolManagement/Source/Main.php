<?php

namespace Source;

use MysqliDb;
use Source\Config\Configuration;
use Source\Helper\InfoMessage;

class Main
{
    public $db;
    public  $response_message;
    function __construct()
    {
        if (!$this->AccessControlAllowOrigin()) {
            exit();
        }
        $this->response_message = new InfoMessage();
    }

    function parseUrl()
    {

        $script_name = $_SERVER['SCRIPT_NAME'];
        $request_uri = $_SERVER['REQUEST_URI'];
        $exploded_script_name = explode("/", $script_name);
        end($exploded_script_name);
        $endpoint = prev($exploded_script_name);
        $exploded_request_uri = array_filter(explode("/", $request_uri));
        $continue = false;
        $identity = $resources = $module = null;
        foreach ($exploded_request_uri as $uri) {
            if ($uri == $endpoint) $continue = true;
            if ($continue) {
                if ($uri == $endpoint) continue;
                if ($resources != null)
                    if (is_numeric($uri)) $identity = $uri;
                    elseif ($module != null) $identity = $uri;
                    else $module = strtok($uri, "?");
                else $resources = $uri;
            }
        }
        return ['resource' => $resources, 'module' => $module, 'identity' => $identity];
    }

    /**Set Access Control AllowOrigin */
    function AccessControlAllowOrigin()
    {
        if (!isset($_SERVER["HTTP_ORIGIN"])) {
            $prefix = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            $_SERVER['HTTP_ORIGIN'] = $prefix . $_SERVER["REMOTE_ADDR"];
        }
        header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Max-Age: 3600');
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
        return true;
    }

    function requestMethod()
    {
        $url =  $this->parseUrl();

        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'get':
                $requestFunction = 'httpGet';
                break;
            case 'post':
                if (strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
                    $requestFunction = 'httpFileUpload';
                } else {
                    $requestFunction = 'httpPost';
                }
                break;
            case 'put':
                $requestFunction = 'httpPut';
                break;
            case 'delete':
                $requestFunction = 'httpDel';
                break;
            default:
                $requestFunction = null;
                break;
        }

        if ($url['resource'] === 'source') {
            $this->getModule($url, $requestFunction);
        } else {
            return $this->response_message->errorMessage('Resource not Found');
        }
    }

    function getModule($url, $request_function)
    {
        /**
         * Check if module is empty
         */
        if (!empty($url['module'])) {

            $this->Configuration = new Configuration();
            $this->db = new MysqliDb($this->Configuration->DatabaseConnection());

            $request_modules = ucfirst($url['module']);
            $class_path = "Source\\Modules\\$request_modules";

            if (!class_exists($class_path)) {
                exit($this->response_message->errorMessage('Module not Found'));
            } else {

                $requestPayload = $this->payload();
                $this->Module = new $class_path($this->db);

                if ($request_function === 'httpPost' || $request_function === 'httpGet') {
                    if ($request_function === 'httpGet') {
                        $payload = $_GET;
                    } else {
                        $payload = $requestPayload;
                    }
                    return $this->Module->$request_function($payload);
                }

                if (empty($url['identity'])) {
                    if ($url['resource'] === 'source' && $request_function === 'httpDel') {
                        return $this->Module->$request_function(null, []);
                    }
                    exit();
                }

                return $this->Module->$request_function($url['identity'], $requestPayload);
            }
        }
    }


    function payload()
    {
        $payload = array();

        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $payload);
        }
        $body = file_get_contents("php://input");
        $content_type = false;
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = strtolower($_SERVER['CONTENT_TYPE']);
        }

        switch ($content_type) {
            case "application/json;charset=utf-8":
            case "application/json":
                return $this->convertIntoJson($body);
                break;
            case "application/x-www-form-urlencoded":
                return 'Content Type : application/x-www-form-urlencoded is not supported';
                break;
            case strtok($content_type, ';') == 'multipart/form-data':
                return ['payload' => $_POST, 'form_data' => $_FILES];
                break;
            default:
                return false;
                break;
        }
    }

    function convertIntoJson($json_string)
    {
        $json_decode = json_decode($json_string, true);
        return is_array($json_decode) ? $json_decode : array();
    }
}
