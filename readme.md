
<h1 align="center">Ratricat</h1>
<p  align="center">
<img  width="512" src="https://github.com/WEBDERNargor/ratricat/blob/main/Ratricat.jpg?raw=true"  alt="Material Bread logo">
</p>
Ratricat is a small framework in php language. It uses MVC+Routes architecture. Models Cotrollers Views and Routers connect to mysql pdo database.
 

# How to install

 - Install [composer](https://getcomposer.org/)
 - Install PHP and MySql, recommended as [Xampp](https://www.apachefriends.org/).
 - Install the Ratricat project
    ```bash 
    composer create-project webdernargor/ratricat <project-name>
    ```

## Documention

The document is under preparation and will be available soon.

### Basic use
- Route create file .php inside folder routes
```php
<?php
addRoute('get',  '/profile',  'profile',  'App\Controllers\HomeController@index');
or
addRoute('get',  '/',  'home',  function(){
echo "home page";
});
```
- Middleware create file .php inside folder middlewares
```php
<?php
namespace App\Middlewares;

class LoginMiddleware
{
    public function handle()
    {
        if(!isset($_SESSION['user'])){
            header('Location: /login');
            exit;
        }
    }
}
```
-use middleware in route
```php
<?php
addRouteMiddleware('get','/profile','App\Middlewares\LoginMiddleware@handle');
or
addRouteMiddleware('get','/profile',  function(){
echo "home page";
});
```
- Controller create php file insise app/Controllers/<your_controller>.php
```php
<?php

namespace App\Controllers;

use App\Models\User;

class HomeController
{
    protected $user;

    public function __construct()
    {
        global $app;
        $this->user = new User($app->db);
    }

    public function index()
    {
        $users = $this->user->all();
        return VIEW('home', ['users' => $users]);
    }
}
```

- Model create php files inside app/Models/<Your_model>.php
```php
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

    public function all()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
```
- View Create blade.php inside app/Views/<your_view>.blade.php

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    User list
    <ul>
        @foreach($users as $user)
        <li>{{ $user->name }}</li>
        @endforeach
    </ul>
</body>
</html>
```
Learn more about how to use blade engine at : [Click](https://laravel.com/docs/5.8/blade)
## Authors

- [@WEBDERNargor](https://github.com/WEBDERNargor)