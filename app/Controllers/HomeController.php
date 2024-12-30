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
    
        // echo 'upload_max_filesize = ' . ini_get('upload_max_filesize') . "<br>";
        // echo 'post_max_size = ' . ini_get('post_max_size');
     
        
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


        public function upload(){
            
            return VIEW('user.upload', []);
        }
}