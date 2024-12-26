<?php

namespace App\Console;

class Migrate
{
    protected $pdo;
    protected $migrationsPath;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = __DIR__ . '/../../database/migrations/';
    }

    public function migrate()
    {
        $migration_lists = [];
        $files = glob($this->migrationsPath . '*.php');

        foreach ($files as $file) {
            $migration = include $file;
            $tableName = $this->getTableNameFromFile($file);
            
            if (!$this->tableExists($tableName)) {
                $migration['up']($this->pdo);
                array_push($migration_lists, basename($file));
            } else {
                echo "ตาราง '$tableName' มีอยู่แล้ว ข้ามการสร้าง\n";
            }
        }
        echo json_encode([
            'success' => "success",
            'message' => "migration successfully",
            'migration_lists' => $migration_lists
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function getTableNameFromFile($filePath)
    {
        $fileName = basename($filePath, '.php');
        $parts = explode('_', $fileName);
        // สมมติว่าชื่อตารางอยู่ในส่วนสุดท้ายของชื่อไฟล์
        return end($parts);
    }

    protected function tableExists($tableName)
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :tableName");
        $stmt->execute(['tableName' => $tableName]);
        return $stmt->rowCount() > 0;
    }

    public function rollback()
    {
        $migration_lists = [];
        $files = array_reverse(glob($this->migrationsPath . '*.php'));

        foreach ($files as $file) {
            $migration = include $file;
            $migration['down']($this->pdo);
            array_push($migration_lists, basename($file));
        }

        echo json_encode([
            'success' => "success",
            'message' => "rollback successfully",
            'migration_lists' => $migration_lists
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
