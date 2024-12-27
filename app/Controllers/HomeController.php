<?php

namespace App\Controllers;


class HomeController
{
    protected $user;

    public function __construct()
    {
        global $app;
    }

    public function index()
    {
        
        return VIEW('user.home', []);
    }
    public function login(){
    return VIEW('user.login', []);
    }

    public function register(){
        return VIEW('user.register', []);
        }

    public function logout(){
        deleteCookie('login_token');
        return redirect('/');
        }
}