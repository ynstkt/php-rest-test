<?php
namespace rest\infrastructure\repositories;

use rest\domain\models\Item;
use rest\domain\repositories\IItemRepository;
use rest\usecases\{ItemNotFoundException, DatabaseException};

/**
 * @codeCoverageIgnore
 */
class ItemRepository implements IItemRepository {

    private $db;

    function __construct() {
        $dbHost = getenv('DB_HOST');
        $dbPort = getenv('DB_PORT');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASS');
        $dbName = getenv('DB_NAME');
        $dsn = 'mysql:dbname='.$dbName.';host='.$dbHost.';port=.'.$dbPort.';';
    
        $this->db = new \PDO($dsn, $user, $password);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);      
    }

    public function getAll(): array {
        try {
            $sql = 'select * from items';
            $stt = $this->db->prepare($sql);
            $stt -> execute();
        
            while($row = $stt->fetch(\PDO::FETCH_ASSOC)) {
                $arr[] = new Item($row['id'], $row['name']);
            }
        
            return $arr;
        } catch(\PDOException $e) {
            throw new DatabaseException("database error", 1, $e);
        }
    }
    
    public function getById(string $id): Item {
        try {            
            $sql = 'select * from items where id = :id';
            $stt = $this->db->prepare($sql);
            $stt->bindValue(':id', $id);
            $stt->execute();
        
            $row = $stt->fetch(\PDO::FETCH_ASSOC);
            if(!empty($row)){
                return new Item($row['id'], $row['name']);
            } else {
                throw new ItemNotFoundException('item not found');
            }
        } catch(\PDOException $e) {
            throw new DatabaseException("database error", 1, $e);
        }
    }
    
    public function create(Item $item) {
        try {
            $sql = 'insert into items(id, name) values (:id, :name)';
            $stt = $this->db->prepare($sql);
            $stt->bindValue(':id', $item->getId());
            $stt->bindValue(':name', $item->getName());
            $res = $stt->execute();
            if ($res) {
                return;
            } else {
                throw new DatabaseException("insert failed");
            }
        } catch(\PDOException $e) {
            throw new DatabaseException("database error", 1, $e);
        }     
    }
    
    public function update(Item $item) {
        $this->getById($item->getId());
        try {
            $sql = 'update items set name=:name where id = :id';
            $stt = $this->db->prepare($sql);
            $stt->bindValue(':id', $item->getId());
            $stt->bindValue(':name', $item->getName());
            $res = $stt->execute();
            if ($res) {
                return;
            } else {
                throw new DatabaseException("update failed");
            }
        } catch(\PDOException $e) {
            throw new DatabaseException("database error", 1, $e);
        }    
    }
    
    public function delete(string $id) {
        try {
            $sql = 'delete from items where id = :id';
            $stt = $this->db->prepare($sql);
            $stt->bindValue(':id', $id);
            $stt->execute();
        
            $stt->fetch(\PDO::FETCH_ASSOC);
            return;
        } catch(\PDOException $e) {
            throw new DatabaseException("database error", 1, $e);
        }  
    }
}
