<?php

namespace App\Controllers;
use PDO;
use App\Models\Custom;
use App\Controllers\ServiceController;
class HomeController
{
    protected $sql;
    protected $service;
    public function __construct()
    {
        global $app;
        $this->sql = new Custom($app->db);
        $this->service=new ServiceController();
    }

    public function index()
    {
        
        return VIEW('user.home', []);
    }
    public function myfile()
    {
        
        return VIEW('user.allfile', []);
    }
    public function share($id){
    $file=$this->sql->single("SELECT *,CONCAT(`users`.`u_fname`,' ',`users`.`u_lname`) as `u_fullname` FROM `files` JOIN `users` ON `files`.u_id=`users`.`u_id` WHERE `file_id`=?",[$id],PDO::FETCH_OBJ);
        return VIEW('user.singlefile', ['file'=>$file]);
    }
    public function login(){
    
    return VIEW('user.login', []);
    }

    public function loginpage($page){
        return VIEW('user.login', ['page'=>$page]);
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

        public function profile(){
        $token=getCookieValue('login_token');
        $user_login=$this->service->verifyTokenServer($token);
        return VIEW('user.profile', ["user_login"=>$user_login]);
        }
}