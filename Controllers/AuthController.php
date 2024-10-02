<?php

namespace Controllers;

use Database\Database;
class AuthController extends Database{

    public function validataToken(){
        $token = $this->getToken();
        if(is_null($token)){
            $this->unAuthorizeUser();die;
        }
        $sql = "SELECT Id FROM users WHERE token =  ?";
        $param = [
            $token
        ];
        $stmt = $this->executeStatement($sql , $param);
        $result = $stmt->get_result();
        if($result->num_rows != 1){
            $this->unAuthorizeUser();die;
        }
        return true;
    }
    public function getToken(){

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        // var_dump($authHeader);die;
        if(is_null($authHeader)){
            return null;
        }
        if(substr($authHeader ,0 ,strlen('Bearer ')) !== 'Bearer '){
            return null;
        }
        $token = substr($authHeader,7);
        if(is_null($token) || $token == ''){
            return null;
        }
        return $token;
    }
    public function unAuthorizeUser(){
        $respons = [
            'status' => 'Unauthorized User',
            'error_code' => '401'
        ];
        http_response_code(401);
        echo json_encode($respons);
    }
}