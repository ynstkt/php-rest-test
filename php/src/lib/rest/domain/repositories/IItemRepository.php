<?php
namespace rest\domain\repositories;

use rest\domain\models\Item;

interface IItemRepository {
    function getAll(): array;
    function getById(string $id): Item;
    function create(Item $item);
    function update(Item $item);
    function delete(string $id);
}
