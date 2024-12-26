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
addRoute('get', '/login', 'login', 'App\Controllers\HomeController@login');
addRoute('get', '/register', 'register', 'App\Controllers\HomeController@register');