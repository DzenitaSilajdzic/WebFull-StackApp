<?php

require_once __DIR__ . '/../../rest/services/AuthService.php';

Flight::route('POST /auth/register', function() {
    $data = Flight::request()->data->getData();
    
    $auth_service = new AuthService(); 
    $response = $auth_service->register($data);
    
    if($response['success']) {
        Flight::json([
            'message' => 'User registered successfully',
            'data' => $response['data']
        ], 201);
    } else {
        Flight::json(['error' => $response['error']], 400);
    }
});

Flight::route('POST /auth/login', function() {
    $data = Flight::request()->data->getData();
    
    $auth_service = new AuthService();
    
    $response = $auth_service->login($data);
    
    if($response['success']) {
        Flight::json($response['data'], 200);
    } else {
        Flight::json(['error' => $response['error']], 401);
    }
});
?>