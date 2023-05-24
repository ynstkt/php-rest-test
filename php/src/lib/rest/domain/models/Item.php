<?php
namespace rest\domain\models;

class Item {
    private string $id;
    private string $name;

    public function __construct(string $id, string $name) {
        if(!preg_match('/^[0-9]+$/', $id)) {
            throw new \InvalidArgumentException('invalid id');
        }
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
}
