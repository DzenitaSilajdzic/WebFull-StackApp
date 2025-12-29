<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../../data/Roles.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService extends baseService
{
    private $auth_dao;

    public function __construct()
    {
        $this->auth_dao = new AuthDao();
        parent::__construct($this->auth_dao);
    }

    public function register($entity)
    {
        $user_by_email = $this->auth_dao->get_user_by_email($entity['email']);
        if ($user_by_email) {
            return ['success' => false, 'error' => 'Email is already taken.'];
        }

        $user_by_name = $this->auth_dao->get_user_by_username($entity['username']);
        if ($user_by_name) {
            return ['success' => false, 'error' => 'Username is already taken.'];
        }

        $entity['password'] = password_hash($entity['password'], PASSWORD_BCRYPT);
        
        if (!isset($entity['role'])) {
            $entity['role'] = 'visitor';
        }

        $created_user = parent::add($entity);
        
        unset($created_user['password']);

        return ['success' => true, 'data' => $created_user];
    }

    public function login($entity)
    {
        $user = null;
        
        if (isset($entity['email'])) {
            $user = $this->auth_dao->get_user_by_email($entity['email']);
        } elseif (isset($entity['username'])) {
            $user = $this->auth_dao->get_user_by_username($entity['username']);
        }

        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }
        
        if (!password_verify($entity['password'], $user['password'])) {
            return ['success' => false, 'error' => 'Invalid password.'];
        }

        unset($user['password']);

        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];

        $token = JWT::encode($jwt_payload, Config::JWT_SECRET(), 'HS256');

        return ['success' => true, 'data' => ['token' => $token, 'user' => $user]];
    }
}
?>