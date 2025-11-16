<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/StudioDao.php';

class StudioService extends BaseService {
    public function __construct() {
        $dao = new StudioDao();
        parent::__construct($dao);
    }

    /**
     * active studios for Add Anime
     */
    public function get_active_studios() {
        return $this->dao->getAllActiveStudios();
    }
   
}