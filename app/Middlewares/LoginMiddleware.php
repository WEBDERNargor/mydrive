<?php

namespace App\Middlewares;
use App\Controllers\ServiceController;


class LoginMiddleware
{
    private $service;
    public function __construct()
    {
        $this->service=new ServiceController();
    }
    public function handle()
    {
       

        $token=getCookieValue('login_token');
        if ($token==null){
          redirect('/login');
            exit();
        }

        $user_login=$this->service->verifyTokenServer($token);
        if (!isset($user_login['m_id'])){
            redirect('/login');
            exit();
        }
    }

    public function checkalreadylogin()
    {
        $token=getCookieValue('login_token');
        if ($token!=null){
        $user_login=$this->service->verifyTokenServer($token);
        if (isset($user_login['m_id'])){
            redirect('/');
            exit();
        }
    }
    }

    
}
