<?php
addRoute('post', '/api/login', 'login_api', 'App\Controllers\ServiceController@login');
addRoute('post', '/api/register', 'register_api', 'App\Controllers\ServiceController@register');
// File routes
addRoute('post', '/api/upload', 'upload_api', 'App\Controllers\FileController@upload');
addRoute('post', '/api/upload-chunk', 'uploadchunk_api', 'App\Controllers\FileController@uploadChunk');
addRoute('post', '/api/getfiles', 'getallfile_api', 'App\Controllers\FileController@get_file_data');
addRoute('post', '/api/getfiles', 'change_password_api', 'App\Controllers\ServiceController@change_password');
addRoute('delete', '/api/deletefile', 'deletefile_api', 'App\Controllers\FileController@deletefile_api');
addRoute('patch', '/api/updatefilepublic', 'updatefilepublic_api', 'App\Controllers\FileController@updatefilepublic_api');