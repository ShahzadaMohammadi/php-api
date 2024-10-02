<?php

namespace Controllers;

use Database\Database;
use Controllers\AuthController;
use Traits\SanitizerTrait as san;

class UserController extends Database{

    public function getIdByToken($token){
        $param = [
            $token
        ];
        $sql = "SELECT * FROM users WHERE token = ?";
        $stmt = $this->executeStatement($sql, $param);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['id'];
    }

    public function hasAccess($quote_id){
        $param = [
            $quote_id
        ];
        $sql = "SELECT user_id FROM quotes WHERE id = ?";
        $stmt = $this->executeStatement($sql, $param);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $auth = new AuthController();
        $token = $auth->getToken();
        $user_id = $this->getIdByToken($token);
        if($row['user_id'] != $user_id){
            return false;
        }
        return true;
    }

    public function register(){
        if(!array_key_exists('email',$_POST)){
            $respons = [
                'status' => 'error',
                'messege' => 'Enter your email address'
            ];
            http_response_code(404);
            echo json_encode($respons);die;
        }
        $post = san::sanitizeInput($_POST);
        if(!filter_var($post['email'],FILTER_VALIDATE_EMAIL)){
            $respons = [
                'status' => 'error',
                'messege' => 'Enter valid email address'
            ];
            http_response_code(404);
            echo json_encode($respons);die;
        }
        $email = $post['email'];
        $emailArray = explode('@',$email);
        $name = $emailArray[0];
        echo $name;
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO users (name,email,token) VALUES (?,?,?)";
        $param = [
            $name,
            $email,
            $token
        ];
        $stmt =$this->executeStatement($sql,$param);
        if($stmt->affected_rows == 1):
            $respons = [
                'status' => 'ok',
                'message' => 'user  registeration successfully.'
            ];
            http_response_code(201);
        endif;
        echo json_encode($respons);

    }
}