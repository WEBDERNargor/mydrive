<?php
session_start();
ob_start();
$routerlist = [];
require_once __DIR__ . '/../vendor/autoload.php'; // โหลด autoload ของ Composer
require_once __DIR__ . '/function.php';
use Bramus\Router\Router;
use App\Console\Migrate;
use App\core\CustomBlade;

loadEnv(__DIR__ . '/../.env');
// เช็คว่าถูกเรียกจาก CLI (Command Line Interface)
if (php_sapi_name() == "cli") {
    $config = require_once __DIR__ . '/../config/database.php';
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']};port={$config['port']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $migrate = new Migrate($pdo);

    if ($argv[1] == 'migrate') {
        $migrate->migrate();
    } elseif ($argv[1] == 'rollback') {
        $migrate->rollback();
    }
    exit;
}

// ตั้งค่า Router
$router = new Router();
$folder_routes = __DIR__ . "/../routes/";
$files_routes = glob($folder_routes . "*.php");
foreach ($files_routes as $phpFile_routes) {
    require($phpFile_routes);
}

// ตั้งค่า Blade สำหรับการแสดงผล
$views = __DIR__ . '/../app/Views';
$cache = __DIR__ . '/../storage/cache';
$config = [];





$config = array_merge(
    $config, // อาเรย์เริ่มต้นว่าง
    require __DIR__ . '/../config/general.php',
    require __DIR__ . '/../config/devtools.php',
    require __DIR__ . '/../config/database.php'
);
$default_script='';

if($config['debug']=='true'){
    $router->get('/system/devtools', function () use($routerlist)   {
        require(__DIR__ . '/../system/system.php');
        });



        $router->get('/system/{extension}/{name}', function ($extension, $name) {
            if ($extension == 'css') {
                $filesDir = __DIR__ . '/../system/css/';
                $filepath = realpath($filesDir . $name . ".css");
                $contentType = 'text/css';
            } else if ($extension == 'js') {
                $filesDir = __DIR__ . '/../system/js/';
                $filepath = realpath($filesDir . $name . ".js");
                $contentType = 'application/javascript';
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Invalid file type']);
                exit;
            }
        
            if ($filepath && file_exists($filepath)) {
                sendFile($filepath, $contentType);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'File not found']);
                exit;
            }
        });

        $router->get('/devtools/image/{name}/{extension}', function ($name,$extension) {
            $filesDir = __DIR__ . '/../system/image/';  // กำหนดเส้นทางไปยังโฟลเดอร์ที่เก็บไฟล์

            $filepath = realpath($filesDir . $name . "." . $extension);
        
            if ($filepath && file_exists($filepath)) {
                sendFile($filepath, mime_content_type($filepath));
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'File not found']);
                exit;
            }
        });
    $default_script='<script src="'.URL().'/system/js/system"></script>';
}
$blade = new CustomBlade($views, $cache, $default_script);

// ตั้งค่า PDO สำหรับการเชื่อมต่อกับฐานข้อมูล


$dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['username'], $config['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// จัดการ Application Container
$app = new stdClass();
$app->router = $router;
$app->blade = $blade;
$app->db = $pdo;

// ฟังก์ชัน VIEW เพื่อใช้ใน Controller
function VIEW($view, $data = [])
{
    global $app;
    echo $app->blade->render($view, $data);
}



function NAME()
{
    global $config;
    return isset($config['name']) ? $config['name'] : '';
}
function sendFile($filepath, $contentType)
{
    if (file_exists($filepath)) {
        header('Content-Type: ' . $contentType);
        readfile($filepath);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'File not found']);
        exit;
    }
}




$router->get('/files/{name}/{extension}', function ($name, $extension) {
    $filesDir = __DIR__ . '/../files/';  // กำหนดเส้นทางไปยังโฟลเดอร์ที่เก็บไฟล์

    $filepath = realpath($filesDir . $name . "." . $extension);

    if ($filepath && file_exists($filepath)) {
        sendFile($filepath, mime_content_type($filepath));
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'File not found']);
        exit;
    }
});






return $app;
