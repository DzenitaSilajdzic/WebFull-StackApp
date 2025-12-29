<?php
require_once __DIR__ . '/baseDao.php';

class studioDao extends baseDao
{
    public function __construct() {
        parent::__construct("studios");
    }
    /**
     * for Add Anime page
     */
    public function getAllActiveStudios()
    {
        return $this->query("SELECT * FROM studios", []);
    }
}