<?php
function pre_r($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remove surrounding quotes if any
        $value = trim($value, "'\"");

        // Set environment variable
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

function URL()
{
    global $config;
    return isset($config['url']) ? $config['url'] : '';
}



function addRoute($method, $route, $name, $callback)
{
    $config = require __DIR__ . '/../config/general.php';
    $url = $config['url'] . $route;
    $method = strtolower($method);
    $GLOBALS['routerlist'][] = ["method" => $method, "route" => $route, "name" => $name, "url" => $url];
    $GLOBALS['router']->$method($route, $callback);

}


// Function to add a route and store its name
function addMatchRoute($method, $route, $name, $callback)
{
    $config = require __DIR__ . '/../config/general.php';
    $url = $config['url'] . $route;
    $method = strtoupper($method);
    $GLOBALS['routerlist'][] = ["method" => $method, "route" => $route, "name" => $name, "url" => $url];
    $GLOBALS['router']->match($method, $route, $callback);
}

function addRouteMiddleware($method, $route, $callback)
{

    $method = strtoupper($method);
    $GLOBALS['router']->before($method, $route, $callback);
}
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

function route($name, $params = [])
{
    global $routerlist;
    
    foreach ($routerlist as $route) {
        if ($route['name'] === $name) {
            $url = $route['url'];
            
            // แทนที่พารามิเตอร์ในURL (ถ้ามี)
            foreach ($params as $key => $value) {
                $url = str_replace(":$key", $value, $url);
            }
            
            return $url;
        }
    }
    
    throw new Exception("Route with name '$name' not found.");
}
// ฟังก์ชันสำหรับตั้งค่าคุกกี้
function setCookieValue($name, $value, $days) {
    $expires = time() + ($days * 86400); // 86400 วินาทีในหนึ่งวัน
    setcookie($name, $value, $expires, "/"); // path = "/" หมายถึงคุกกี้สามารถเข้าถึงได้ทั่วทั้งเว็บไซต์
}

// ฟังก์ชันสำหรับอ่านค่าคุกกี้
function getCookieValue($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
}

// ฟังก์ชันสำหรับลบคุกกี้
function deleteCookie($name) {
    setcookie($name, "", time() - 3600, "/"); // ตั้งเวลาหมดอายุให้ย้อนหลังไปหนึ่งชั่วโมง
}

function jsonResponse($data, $statusCode = 200)
{
    header("Content-type: application/json; charset=utf-8");
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function fillterpermission($permission){
    if($permission<=100 ){
          return "user";
    }elseif($permission>100 and $permission<=500){
         return "employee";
    }elseif($permission>500 and $permission<=9999){
     return "admin";
    }else{
     return "user";
    }
    
 }
