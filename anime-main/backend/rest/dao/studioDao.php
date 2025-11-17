<?php
require_once 'BaseDao.php';

class StudioDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("studios");
    }

    /**
     * for Add Anime page
     */
    public function getAllActiveStudios()
    {
        return $this->query("SELECT id, name FROM studios WHERE status = 'working' ORDER BY name ASC", []);
    }
}