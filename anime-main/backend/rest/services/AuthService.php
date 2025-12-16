<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../../data/Roles.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService extends BaseService
{
    private $auth_dao;

    public function __construct()
    {
        $this->auth_dao = new AuthDao();
        parent::__construct($this->auth_dao);
    }

    public function register($entity)
    {

        $entity['password'] = password_hash($entity['password'], PASSWORD_BCRYPT);
        
        if (!isset($entity['role'])) {
            $entity['role'] = 'visitor';
        }

        $new_user_id = parent::add($entity);
        $entity['id'] = $new_user_id;
        unset($entity['password']);

        return ['success' => true, 'data' => $entity];
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