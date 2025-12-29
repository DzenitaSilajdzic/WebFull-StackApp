<?php
require_once __DIR__ . '/../rest/config.php';
require_once __DIR__ . '/../data/Roles.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {

    public function verifyToken($header) {
        if (empty($header)) {
             if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                 $header = $_SERVER['HTTP_AUTHORIZATION'];
             } elseif (function_exists('apache_request_headers')) {
                 $requestHeaders = apache_request_headers();
                 if (isset($requestHeaders['Authorization'])) {
                     $header = $requestHeaders['Authorization'];
                 }
             }
        }

        if (empty($header) || strpos($header, 'Bearer ') !== 0) {
            Flight::halt(401, json_encode(['message' => "Missing Authorization header"]));
            return;
        }

        $token = substr($header, 7);

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

            $user_data = isset($decoded->user) ? $decoded->user : $decoded;

            Flight::set('user', $user_data);
            Flight::set('jwt_token', $token);

            return TRUE;

        } catch (\Exception $e) {
            Flight::halt(401, json_encode(['message' => "Invalid Token: " . $e->getMessage()]));
            return;
        }
    }

    public function authorize($required_role) {
        $user = Flight::get('user');

        if (!$user) {
            Flight::halt(403, json_encode(['message' => 'Forbidden - User not authenticated']));
            die;
        }

        $role = null;
        if (is_object($user) && isset($user->role)) {
            $role = $user->role;
        } elseif (is_array($user) && isset($user['role'])) {
            $role = $user['role'];
        }

        if ($role !== $required_role) {
            Flight::halt(403, json_encode(['message' => 'Forbidden - Insufficient permissions']));
            die;
        }
    }
}
?>