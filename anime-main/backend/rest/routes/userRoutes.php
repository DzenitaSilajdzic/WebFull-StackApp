<?php

/**
* @OA\Post(
* path="/user/register",
* tags={"user"},
* summary="Register a new user (Sign Up)",
* @OA\RequestBody(
* required=true,
* @OA\JsonContent(
* required={"username", "password", "email"},
* @OA\Property(property="username", type="string", example="testuser1", description="Unique username"),
* @OA\Property(property="password", type="string", example="12345", description="User password"),
* @OA\Property(property="email", type="string", example="user@email.com", description="Unique email"),
* @OA\Property(property="name", type="string", example="Test User", description="User's full name (optional)")
* )
* ),
* @OA\Response(
* response=200,
* description="User successfully registered",
* @OA\JsonContent(ref="#/components/schemas/User")
* ),
* @OA\Response(response=400, description="Username or email already exists / Validation error")
* )
*/
Flight::route('POST /user/register', function(){
    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::userService()->register($data));
    } catch (Exception $e) {
        Flight::halt(400, json_encode(['error' => $e->getMessage()]));
    }
});

/**
* @OA\Post(
* path="/user/login",
* tags={"user"},
* summary="Log in a user",
* @OA\RequestBody(
* required=true,
* @OA\JsonContent(
* required={"username", "password"},
* @OA\Property(property="username", type="string", example="testuser1"),
* @OA\Property(property="password", type="string", example="12345")
* )
* ),
* @OA\Response(
* response=200,
* description="User successfully logged in (returns user data and token)",
* @OA\JsonContent(ref="#/components/schemas/User")
* ),
* @OA\Response(response=401, description="Invalid credentials")
* )
*/
Flight::route('POST /user/login', function(){
    try {
        $data = Flight::request()->data->getData();
        // returns user success
        $user = Flight::userService()->login($data);
       
        Flight::json(['user' => $user, 'message' => 'Login successful']);
    } catch (Exception $e) {
        Flight::halt(401, json_encode(['error' => $e->getMessage()]));
    }
});