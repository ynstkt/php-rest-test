<?php
require_once '../../vendor/autoload.php';

use rest\Container;
use rest\api\RestHandler;
use rest\api\InputReader;
use rest\api\items\ItemController;

$controller = new ItemController(Container::getItemService());
$handler = new RestHandler($controller, new InputReader());
$handler->handle();
