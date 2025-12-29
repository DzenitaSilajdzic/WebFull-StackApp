<?php
require_once 'baseDao.php';

class userDao extends baseDao
{
    public function __construct()
    {
        parent::__construct("users");
    }

    /**
     *getUserByUresname for login
     */
    public function getUserByUsername($username)
    {
        return $this->query_unique("SELECT * FROM users WHERE username = :username AND status = 'active'", [':username' => $username]);
    }

    /**
     * getUserByEmail for registration
     */
    public function getUserByEmail($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email AND status = 'active'", [':email' => $email]);
    }
   
    /**
     * sign up
     */
    public function addUser($user)
    {
        return $this->add($user);
    }
}