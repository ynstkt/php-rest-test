<?php
namespace rest\api;

abstract class RestController {
    public abstract function getAll();
    public abstract function getById(string $id);
    public abstract function post(array $data);
    public abstract function put(array $data);
    public abstract function delete(string $id);

    public function response(string $code, array $data = null) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($code);
        if ($data) echo json_encode($data, JSON_PRETTY_PRINT);
    }      
}
