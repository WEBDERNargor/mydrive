<?php

namespace App\Middlewares;
use App\Controllers\ServiceController;


class LoginMiddleware
{
    private $service;
    public function __construct()
    {
        $this->service = new ServiceController();
    }
    public function handle()
    {


        $token = getCookieValue('login_token');
        $arr = ["/myfile", "/upload", "/profile"];
        if ($token == null) {

            if (in_array(get_current_route(), $arr)) {
                redirect('/loginpage' . get_current_route());
                exit();
            }

            redirect('/login');
            exit();
        }

        $user_login = $this->service->verifyTokenServer($token);
        if (!isset($user_login['u_id'])) {
            if (in_array(get_current_route(), $arr)) {
                redirect('/loginpage' . get_current_route());
                exit();
            }
        }
    }

    public function checkalreadylogin()
    {
        $token = getCookieValue('login_token');
        if ($token != null) {
            $user_login = $this->service->verifyTokenServer($token);
            if (isset($user_login['u_id'])) {
                redirect('/');
                exit();
            }
        }
    }


}
