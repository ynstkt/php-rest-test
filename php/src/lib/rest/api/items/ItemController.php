<?php
namespace rest\api\items;

use rest\api\RestController;
use rest\domain\models\Item;
use rest\usecases\{IItemService, ItemNotFoundException, DatabaseException};

class ItemController extends RestController {

    private IItemService $service;

    function __construct(IItemService $service) {
        $this->service = $service;
    }

    function getAll() {
        try {
            $items = $this->service->getAll();
            $itemsArr = [
                'items' => array_map(function($item) {
                    return [
                        'id' => $item->getId(),
                        'name' => $item->getName()
                    ];
                }, $items)
            ];
            parent::response(200, $itemsArr);
        } catch(DatabaseException $e) {
            error_log('接続失敗: ' . $e);
            parent::response(500);
        }  
    }
    
    function getById(string $id) {
        try {
            $item = $this->service->getById($id);
            $itemArr = [
                'id' => $item->getId(),
                'name' => $item->getName()
            ];
            parent::response(200, $itemArr);
        } catch(ItemNotFoundException $e) {
            parent::response(404);
        } catch(DatabaseException $e) {
            error_log('接続失敗: ' . $e);
            parent::response(500);
        }
    }
    
    function post(array $data) {
        $id = $data['id'];
        $name = $data['name'];
    
        try {
            $this->service->create(new Item($id, $name));
            parent::response(201);
        } catch(\InvalidArgumentException $e) {
            error_log('ID不正: ' . $e);
            parent::response(400);
        } catch(DatabaseException $e) {
            error_log('接続失敗: ' . $e);
            parent::response(500);
        }
    }
    
    function put(array $data) {
        $id = $data['id'];
        $name = $data['name'];
    
        try {
            $this->service->update(new Item($id, $name));
            parent::response(204);
        } catch(ItemNotFoundException $e) {
            parent::response(404);
        } catch(DatabaseException $e) {
            error_log('接続失敗: ' . $e);
            parent::response(500);
        }
    }
    
    function delete(string $id) {
        try {
            $this->service->delete($id);
            parent::response(204);
        } catch(DatabaseException $e) {
            error_log('接続失敗: ' . $e);
            parent::response(500);
        }
    }
}


