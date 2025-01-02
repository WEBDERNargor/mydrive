<?php
//route page demo
// addRoute('get', '/', 'home', 'App\Controllers\HomeController@index');
// addRoute('get', '/profile', 'profile', 'App\Controllers\HomeController@profile');
// addRoute('get', '/login', 'login', 'App\Controllers\HomeController@login');
// addRoute('post', '/login_process', 'login_process', 'App\Controllers\HomeController@login_process');
// addRoute('get', '/logout', 'logout', 'App\Controllers\HomeController@logout');
// //middleware demo for profile page only login user can access
// addRouteMiddleware('get','/profile','App\Middlewares\LoginMiddleware@handle');


addRoute('get', '/', 'home', 'App\Controllers\HomeController@index');
addRoute('get', '/myfile', 'myfile', 'App\Controllers\HomeController@myfile');
addRoute('get', '/logout', 'logout', 'App\Controllers\HomeController@logout');
addRoute('get', '/login', 'login', 'App\Controllers\HomeController@login');
addRoute('get', '/loginpage/{page}', 'loginpage', 'App\Controllers\HomeController@loginpage');
addRoute('get', '/register', 'register', 'App\Controllers\HomeController@register');
addRoute('get', '/upload', 'upload', 'App\Controllers\HomeController@upload');
addRoute('get', '/profile', 'profile', 'App\Controllers\HomeController@profile');
addRoute('get', '/share/{id}', 'share', 'App\Controllers\HomeController@share');
addRoute('get', '/file/{filename}/{filetype}', 'getFile', 'App\Controllers\FileController@getFile');
addRoute('get', '/stream/{filename}/{filetype}', 'streamVideo', 'App\Controllers\FileController@streamVideo');
addRoute('get', '/thumnail/{filename}', 'thumnailVideo', 'App\Controllers\FileController@getThumnail');

addRouteMiddleware('get', '/login', 'App\Middlewares\LoginMiddleware@checkalreadylogin');
addRouteMiddleware('get', '/register', 'App\Middlewares\LoginMiddleware@checkalreadylogin');
addRouteMiddleware('get', '/upload', 'App\Middlewares\LoginMiddleware@handle');
addRouteMiddleware('get', '/myfile', 'App\Middlewares\LoginMiddleware@handle');
addRouteMiddleware('get', '/profile', 'App\Middlewares\LoginMiddleware@handle');

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
   
    exit();
});