<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\FileController;

// สร้าง instance ของ FileController
$fileController = new FileController();

// เรียกใช้ฟังก์ชันทำความสะอาดไฟล์เก่า
$fileController->cleanupOldPartFiles();

// บันทึก log
error_log(date('Y-m-d H:i:s') . ": Cleanup old uploads script executed\n", 3, __DIR__ . '/../../storage/logs/cleanup.log');
