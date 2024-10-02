<?php

namespace Controllers;

use Traits\SanitizerTrait as san;
use Database\Database;
use Controllers\AuthController;
use Controllers\UserController;
class QuoteController extends Database{

    protected $table = 'quotes';
    public function index() {
        $sql = "SELECT quotes.*,users.name AS username FROM $this->table INNER JOIN users ON users.id = quotes.user_id ORDER BY id";
        $stmt = $this->executeStatement($sql);
        $rows = $stmt->get_result();

        if($rows->num_rows <=0){
            $response = [
                'status' => 'error',
                'message' => 'no record exists in dbs'
            ];
            http_response_code(404);
        }else{
            $response = [];

            foreach($rows as $row){
            $response[] = $row;
        }
        http_response_code(200);
        }
        echo json_encode($response);

    }

    public function getQuote($param){
        $id = $param['id'];
        
        $sql = "SELECT quotes.* , users.name AS username FROM $this->table INNER JOIN users ON users.id = quotes.user_id WHERE quotes.id =?";
        $sql_param = [$id];
        $stmt = $this->executeStatement($sql,$sql_param);
        $row = $stmt->get_result();
        $row = $row->fetch_assoc();
        
        if(is_null($row)){
            $response = [
                'status' => 'error',
                'message' => 'no record id found',
                'error_code' => '404'
            ];
            http_response_code(404);
        }else {
            $response[] = $row;
            http_response_code(200);
        }
        echo json_encode($response);


    }

    public function store(){
        $auth = new AuthController();
        $token = $auth->getToken();
        $user = new UserController();
        $user_id = $user->getIdByToken($token);
        
        $data = san::sanitizeInput($_POST);

        if(!array_key_exists('quote',$data) || !array_key_exists('author',$data)){
            $response = [
                'status' => 'error',
                'message' => 'Invaild inputs',
                'error_code' => '404'
            ];
            http_response_code(404);
        }else{
            
            $sql = "INSERT INTO $this->table(user_id,quote,author) VALUES (?, ?, ?)";

            $params = [
                $user_id,
                $data['quote'],
                $data['author']
            ];
            $stmt = $this->executeStatement($sql,$params);
            if($stmt->affected_rows == 1){
                $response = [
                    'status' => 'Ok',
                    'quote_id' => $stmt->insert_id,
                    'message' => 'Quote added successfully'
                ];
                http_response_code(201);
            }else{
                $response = [
                    'status' => 'error',
                    'message' => 'con not insert new row'
                ];
                http_response_code(404);
            }
        }
        echo json_encode($response);
    }

    public function update($id) {
        $id = san::sanitizeInput($id['id']);
        $id = (int) $id;

        $user = new UserController();
        $accessId = $user->hasAccess($id);

        $put_data = file_get_contents("php://input");
        parse_str($put_data,$data);
        $data = san::sanitizeInput($data);
        $sql = "UPDATE $this->table SET ";
        $update = [];
        foreach($data as $key => $value){
            $update[] = "$key = '$value'";
        }
        $sql .= implode(', ',$update);
        $sql .= ", updated_at = NOW() WHERE id = ?";
        $param = [
            $id
        ];
        if($accessId){
        $stmt = $this->executeStatement($sql,$param);
        if($stmt->affected_rows == 1) {
            $response = [
                'status' => 'OK',
                'message' => 'update record successfully',
            ];
            http_response_code(200);
        }else{
            $response = [
                'status' => 'error',
                'message' => 'update record filed',
                'error_code' => '404'
            ];
            http_response_code(404);
        }}else{
            $response = [
                'status' => 'error',
                'message' => 'You don\'t have access to update this quote',
                'error_code' => '401'
            ];
            http_response_code(401);
        }
        echo json_encode($response);

    }

    public function delete($id) {
        $id = san::sanitizeInput($id['id']);
        $id = (int) $id;

        $user = new userController();
        $accessId = $user->hasAccess($id);

        $param = [
            $id
        ];
        if($accessId){
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $stmt = $this->executeStatement($sql,$param);
        if($stmt->affected_rows == 1){
            $response = [
                'status' => 'Ok',
                'message' => 'record deleted successfully '.$id
            ];
            http_response_code(200);
        }else{
            $response = [
                'status' => 'error',
                'message' => 'record deleting filed ' . $id,
                'error_code' => '404'
            ];
            http_response_code(404);
        }}else{
            $response = [
                'status' => 'error',
                'message' => 'You don\'t have access to delete this quote',
                'error_code' => '401'
            ];
            http_response_code(401);
        }
        echo json_encode($response);
    }

    public function getQuoteByAuthor($param){
        $param = san::sanitizeInput($param['author']);
        $param = str_replace('%20',' ' , $param);
        $param = "%$param%";
        $param = [
            $param
        ];
        $sql = "SELECT quotes.*,users.name AS username FROM $this->table INNER JOIN users ON users.id = quotes.user_id WHERE author LIKE  ?";
        $stmt = $this->executeStatement($sql , $param);
        $rows = $stmt->get_result();
        if($rows->num_rows <= 0){
            $response = [
                'status' => 'error',
                'message' => 'No record found',
                'error_code' => '404'
            ];
            http_response_code(404);
        }else{
            foreach($rows as $row){
            $response[] = $row;
            }
            http_response_code(200);
        }
        echo json_encode($response);
    }

    public function getQuotesByUserId($id){
        $id = san::sanitizeInput($id['id']);
        $id = (int) $id;
        $param = [
            $id
        ];
        $sql = "SELECT quotes.* , users.name AS username FROM $this->table INNER JOIN users ON users.id = quotes.user_id WHERE user_id = ?";
        $stmt = $this->executeStatement($sql, $param);
        $rows = $stmt->get_result();
        if($rows->num_rows <= 0){
            $response = [
                'status' => 'error',
                'message' => 'record not funod',
                'error_code' => '404'
            ];
            http_response_code(404);
        }else{
            $response = [];
            foreach($rows as $row){
                $response[] = $row;
            }
            http_response_code(200);
        }
        echo json_encode($response);
    }

    public function pagelimetation($num){
        $num = (int) san::sanitizeInput($num['num']);
        $end = $num* 20;
        $start = $end - 20;
        $sql = "SELECT * FROM quotes LIMIT ? ,20";
        $param = [
            $start
        ];
        $stmt = $this->executeStatement($sql, $param);
        $result = $stmt->get_result();
        if($result->num_rows <= 0){
            $response = [
                'status' => 'error',
                'message' => 'There is on record.'
            ];
            http_response_code(404);
        }else{
            $response = [];
            foreach($result as $row){
                $response [] = $row;
            }
            http_response_code(200);
        }
        echo json_encode($response);

    }
    
}