<?php
addRoute('post', '/api/login', 'login_api', 'App\Controllers\ServiceController@login');
addRoute('post', '/api/register', 'register_api', 'App\Controllers\ServiceController@register');
