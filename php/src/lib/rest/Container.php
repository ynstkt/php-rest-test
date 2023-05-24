<?php
namespace rest;

use rest\infrastructure\repositories\ItemRepository;
use rest\usecases\IItemService;
use rest\usecases\ItemService;

/**
 * @codeCoverageIgnore
 */
class Container {
    private static $itemService;

    private function __construct() {}

    public static function getItemService(): IItemService {
        if (!isset(self::$itemService)) {
            $itemRepository = new ItemRepository();
            self::$itemService = new ItemService($itemRepository);
        }
        return self::$itemService;
    }
}