<?php

namespace App\Models;
use PDO;

class User
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function all($fetch = PDO::FETCH_OBJ)
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll($fetch);
    }



    public function find($id, $fetch = PDO::FETCH_OBJ)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch($fetch);
    }

  
  
}