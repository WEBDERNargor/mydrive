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
addRoute('get', '/logout', 'logout', 'App\Controllers\HomeController@logout');
addRoute('get', '/login', 'login', 'App\Controllers\HomeController@login');
addRoute('get', '/register', 'register', 'App\Controllers\HomeController@register');
addRoute('get', '/upload', 'upload', 'App\Controllers\HomeController@upload');
addRoute('get', '/file/{filename}', 'getFile', 'App\Controllers\FileController@getFile');

addRouteMiddleware('get','/login','App\Middlewares\LoginMiddleware@checkalreadylogin');
addRouteMiddleware('get','/register','App\Middlewares\LoginMiddleware@checkalreadylogin');
addRouteMiddleware('get','/upload','App\Middlewares\LoginMiddleware@handle');
