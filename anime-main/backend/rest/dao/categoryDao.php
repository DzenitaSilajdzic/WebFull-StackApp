<?php
require_once 'baseDao.php';

class categoryDao extends baseDao
{
    public function __construct()
    {
        parent::__construct("categories");
    }

    /**
     * for Add Anime page
     */
    public function getAllActiveCategories()
    {
        return $this->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC", []);
    }
   
}