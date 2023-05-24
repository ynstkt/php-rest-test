<?php
namespace rest\usecases;

use rest\domain\models\Item;

interface IItemService {
    public function getAll(): array;
    public function getById(string $id): Item;
    public function create(Item $item);
    public function update(Item $item);
    public function delete(string $id);
}
