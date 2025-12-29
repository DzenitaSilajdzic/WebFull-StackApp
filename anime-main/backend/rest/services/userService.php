<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/userDao.php';

class userService extends baseService {
    public function __construct() {
        $dao = new UserDao();
        parent::__construct($dao);
    }

    /**
     * sign-up 
     * error if username/email exists
     */
    public function register($data) {
        if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
            throw new Exception("Username, password, and email are required.");
        }

        // if user exists
        if ($this->dao->getUserByUsername($data['username'])) {
            throw new Exception("Username already taken.");
        }
        if ($this->dao->getUserByEmail($data['email'])) {
            throw new Exception("Email already registered.");
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // default 
        $data['role'] = $data['role'] ?? 'visitor';
        $data['status'] = $data['status'] ?? 'active';

        return $this->dao->addUser($data);
    }

    /**
     * login
     * return success
     * error failure
     */
    public function login($data) {
        if (empty($data['username']) || empty($data['password'])) {
            throw new Exception("Username and password are required.");
        }

        $user = $this->dao->getUserByUsername($data['username']);

        if (!$user) {
            throw new Exception("Invalid username or password.");
        }
       // compare passwords
        if (password_verify($data['password'], $user['password'])) {
            unset($user['password']);
            return $user;
        } else {
            throw new Exception("Invalid username or password.");
        }
    }
}