<?php

namespace App\Controllers;

use App\Models\Custom;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use Exception;

class ServiceController
{
    protected $sql;

    public function __construct()
    {
        global $app;
        $this->sql = new Custom($app->db);
    }





    public function login()
    {
        try {

            $email = $_POST['email'];
            $password = $_POST['password'];
            $res = $this->sql->param("SELECT *,CONCAT(u_fname,' ',u_lname) as u_fullname FROM users WHERE u_email = ?", [$email]);
            if ($res->rowCount() > 0) {
                $user = $res->fetch(PDO::FETCH_OBJ);
                if (password_verify($password . $user->u_salt, $user->u_password)) {
                    $token = $this->generateJWT($user->u_id, (1 * 24 * 60 * 60 * 1000));
                    $this->jsonResponse(['status' => 'success', 'message' => 'Login successful', "token" => $token]);
                } else {
                    $this->jsonResponse(['status' => 'error', 'message' => 'Invalid password'], 401);
                }
            } else {

                $this->jsonResponse(['status' => 'error', 'message' => 'Invalid email'], 401);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function register()
    {

        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $fname = $data['fname'] ?? null;
        $lname = $data['lname'] ?? null;
        $permission = rand(-500, 100);
        if (!$email || !$password) {
            return $this->jsonResponse(["status" => "error", "message" => "กรุณาระบุอีเมลและรหัสผ่าน"], 400);
        }

        $user = $this->sql->single("SELECT * FROM users WHERE u_email = :email", ["email" => $email]);

        if ($user) {
            return $this->jsonResponse(["status" => "error", "message" => "อีเมลนี้มีผู้ใช้งานแล้ว"], 400);
        }
        $salt = uniqid();
        $password = password_hash($password . $salt, PASSWORD_DEFAULT);

        $this->sql->param("INSERT INTO users (u_email, u_password,u_salt,u_fname,u_lname,u_permission) VALUES (:email, :password,:salt,:fname,:lname,:permission)", ["email" => $email, "password" => $password, "salt" => $salt, "fname" => $fname, "lname" => $lname, "permission" => $permission]);

        return $this->jsonResponse(["status" => "success", "message" => "ลงทะเบียนสำเร็จ"]);
    }


    public function generateJWT($userId, $timeout = 3600)
    {
        $config = include(__DIR__ . '/../../config/general.php');
        $secretKey = $config['jwt_secret']; // ควรเก็บไว้ในไฟล์ config
        $issuedAt = time();
        $expirationTime = $issuedAt + $timeout; // หมดอายุใน 1 ชั่วโมง

        $payload = [
            'user_id' => $userId,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }


    public function verifyUser()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;

        if (!$token) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบ token"], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = $this->verifyToken($token);

        if (!$decoded) {
            return $this->jsonResponse(["status" => "error", "message" => "token ไม่ถูกต้องหรือหมดอายุ"], 401);
        }

        $userId = $decoded->user_id;
        $user = $this->sql->single("SELECT *,CONCAT(u_fname,' ',u_lname) as u_fullname FROM users WHERE u_id = :id", ["id" => $userId]);

        if (!$user) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบผู้ใช้"], 404);
        }

        return $this->jsonResponse([
            "status" => "success",
            "user" => [
                "id" => $user['u_id'],
                "email" => $user['u_email'],
                "name" => $user['u_fullname']
            ]
        ]);
    }

    public function verifyTokenServer($token)
    {
        $decoded = $this->verifyToken($token);
        if ($decoded) {
            $row = $this->sql->single("SELECT *,CONCAT(u_fname,' ',u_lname) as u_fullname FROM users WHERE u_id = :id", ["id" => $decoded->user_id]);
            return $row;
        } else {
            return [];
        }
    }

    private function jsonResponse($data, $statusCode = 200)
    {
        header("Content-type: application/json; charset=utf-8");
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function verifyToken($token)
    {
        $config = include(__DIR__ . '/../../config/general.php');
        $secretKey = $config['jwt_secret']; // ควรเก็บไว้ในไฟล์ config

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function change_password()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;

        if (!$token) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบ token"], 401);
        }
        $token = str_replace('Bearer ', '', $token);
        $user = $this->verifyTokenServer($token);
        if (!isset($user['u_id'])) {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่พบผู้ใช้"], 401);
        }
        if (!isset($_POST['password']) or !isset($_POST['re_password'])) {
            return $this->jsonResponse(["status" => "error", "message" => "กรุณาระบุรหัสผ่าน"], 400);

        }
        $password = $_POST['password'];
        $re_password = $_POST['re_password'];
        if ($password != $re_password) {
            return $this->jsonResponse(["status" => "error", "message" => "รหัสผ่านไม่ตรงกัน"], 400);

        }
        $salt = uniqid();
        $new_password = password_hash($password . $salt, PASSWORD_DEFAULT);
        $res = $this->sql->param("UPDATE users SET u_password=? WHERE u_id=?", [$new_password, $user['u_id']]);
        if ($res) {
            return $this->jsonResponse(["status" => "success", "message" => "เปลี่ยนรหัสผ่านสำเร็จ"], 201);
        } else {
            return $this->jsonResponse(["status" => "error", "message" => "ไม่สามารถเปลี่ยนรหัสผ่านได้"], 500);
        }
    }
}
