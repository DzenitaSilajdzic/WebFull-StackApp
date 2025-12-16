<?php
require_once __DIR__ . '/../rest/config.php';
require_once __DIR__ . '/../data/Roles.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {


    public function verifyToken($header) {
        if (empty($header) || strpos($header, 'Bearer ') !== 0) {
            Flight::halt(401, "Missing or invalid Authorization header. Format: 'Bearer [token]'");
            return;
        }

        $token = substr($header, 7);

        try {
            $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

            Flight::set('user', $decoded_token->user);
            Flight::set('jwt_token', $token);

            return TRUE;

        } catch (\Exception $e) {
            Flight::halt(401, "Invalid Token: " . $e->getMessage());
            return;
        }
    }


    public function authorize($required_role) {
        $user = Flight::get('user');

        if (!$user || !isset($user->role)) {
            Flight::halt(403, json_encode(['message' => 'Forbidden - No role assigned']));
            die;
        }

        if ($user->role !== $required_role) {
            Flight::halt(403, json_encode(['message' => 'Forbidden - Insufficient permissions']));
            die;
        }
    }
}
?>