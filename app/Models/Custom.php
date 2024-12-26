<?php

namespace App\Models;
use PDO;

class Custom
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }




    public function param($sql, $data = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
       return $stmt;
    }
    public function query($sql)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
       return $stmt;
    }

    public function single($sql,$params=[],$fetchStyle=PDO::FETCH_ASSOC)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch($fetchStyle);
    }
  
}