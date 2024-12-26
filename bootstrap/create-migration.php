<?php

// รับชื่อตารางจากอาร์กิวเมนต์
$tableName = $argv[1] ?? null;

if (!$tableName) {
    die(json_encode([
        'success' => "error",
        'message' => 'ไม่พบชื่อตาราง',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

$directory = __DIR__ . '/../database/migrations/';
if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

$prefix = date('Y_m_d_His');
$filename = "{$prefix}_create_{$tableName}_table.php";

$template = <<<EOT
<?php

return [
    'up' => function(\$pdo) {
        \$sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        \$pdo->exec(\$sql);
    },
    'down' => function(\$pdo) {
        \$sql = "DROP TABLE {$tableName}";
        \$pdo->exec(\$sql);
    }
];
EOT;

file_put_contents($directory . $filename, $template);

echo json_encode([
    'success' => "success",
    'message' => 'สร้างไฟล์ migration แล้ว',
    'filename' => $filename
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
