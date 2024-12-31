<?php
return [
    "up" => function ($pdo) {
 
        // สร้างตาราง users ถ้ายังไม่มีอยู่
        $pdo->exec("
CREATE TABLE `users` (
  `u_id` float NOT NULL,
  `u_socail_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `u_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `u_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `u_salt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `u_fname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `u_lname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `u_permission` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0001',
  `u_login_type` set('website','facebook','google') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'website',
  `u_created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
           
        ");
        $pdo->exec("ALTER TABLE `users` ADD PRIMARY KEY (`u_id`);");
        $pdo->exec("INSERT INTO `users` (`u_id`, `u_socail_id`, `u_email`, `u_password`, `u_salt`, `u_fname`, `u_lname`, `u_permission`, `u_login_type`, `u_created_at`) VALUES
(1, NULL, 'tomhorrorza@gmail.com', '$2y$10$yQGvhsNj9lcusMZMvX4O9ufbiLYT/EmaD8G/mQwfJi24VGJWdvzKu', '67737c2064d44', 'Ditsarut', 'Sukkong', '0001', 'website', '2024-12-31 12:07:44');");
    },

    "down" => function ($pdo) {
        // ลบตาราง users ถ้ามีอยู่
        $pdo->exec("DROP TABLE IF EXISTS users");
    }
];

