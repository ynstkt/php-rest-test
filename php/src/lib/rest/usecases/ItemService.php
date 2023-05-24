<?php
namespace rest\usecases;

use rest\usecases\IItemService;
use rest\domain\models\Item;
use rest\domain\repositories\IItemRepository;

class ItemService implements IItemService {

    private IItemRepository $repository;

    function __construct(IItemRepository $repository) {
        $this->repository = $repository; 
    }

    public function getAll(): array {
        return $this->repository->getAll();
    }

    public function getById(string $id): Item {
        return $this->repository->getById($id);
    }

    public function create(Item $item) {
        return $this->repository->create($item);
    }

    public function update(Item $item) {
        return $this->repository->update($item);
    }

    public function delete(string $id) {
        return $this->repository->delete($id);
    }
}
