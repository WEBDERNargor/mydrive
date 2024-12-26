<?php

namespace App\Middlewares;

use App\Core\Middleware;

class LoginMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
            exit();
        }
    }
}
