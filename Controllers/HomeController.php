<?php

namespace Controllers;

class HomeController{

    public function home() {
        $response = [
            'status' => 'OK',
            'message' => 'wellcome home page'
        ];
        echo json_encode($response);
    }
}