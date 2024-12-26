<?php
return [
    "up" => function ($pdo) {
 
        // สร้างตาราง users ถ้ายังไม่มีอยู่
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        //email : admin@admin.com 
        //password admin
        $pdo->exec("INSERT INTO `users` (`name`, `email`, `password`) VALUES ('admin', 'admin@admin.com', '$2y$10$50v/esF5care8/U.b4MmIeMncB1.t6DvD2fTE3E0Wi8KTSDidBUpG')");
    },

    "down" => function ($pdo) {
        // ลบตาราง users ถ้ามีอยู่
        $pdo->exec("DROP TABLE IF EXISTS users");
    }
];

