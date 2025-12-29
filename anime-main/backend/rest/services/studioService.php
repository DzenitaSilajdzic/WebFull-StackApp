<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/studioDao.php';

class StudioService extends baseService {
    private $studio_dao;

    public function __construct() {
        $this->studio_dao = new StudioDao();
        parent::__construct($this->studio_dao);
    }

    public function get_all_studios() {
        return $this->studio_dao->getAllActiveStudios(); 
    }
}
?>