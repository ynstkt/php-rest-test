<?php
namespace rest\api;

use rest\api\RestController;
use rest\api\InputReader;

class RestHandler {
    private RestController $controller;
    private InputReader $reader;

    public function __construct(RestController $controller, InputReader $reader) {
        $this->controller = $controller;
        $this->reader = $reader;
    }

    public function handle() {
        preg_match('|' . dirname($_SERVER['SCRIPT_NAME']) . '/([\w%/]*)|', $_SERVER['REQUEST_URI'], $matches);
        $id = htmlspecialchars($matches[1]);

        try {
            switch (strtolower($_SERVER['REQUEST_METHOD'])) {
                case 'get':
                    if ($id) {
                        $this->controller->getById($id);
                    } else {
                        $this->controller->getAll();
                    }
                    break;
                case 'post':
                    $json = $this->reader->getInputStream();
                    $postData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    $this->controller->post($postData);
                    break;
                case 'put':
                    $json = $this->reader->getInputStream();
                    $putData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    $this->controller->put($putData);
                    break;
                case 'delete':
                    $this->controller->delete($id);
                    break;
            }
        } catch (\JsonException $e) {
            error_log('JSON不正: '.$e);
            $this->controller->response(400);
        }
    }
}
