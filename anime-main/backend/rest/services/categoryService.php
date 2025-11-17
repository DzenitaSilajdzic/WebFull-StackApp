<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService extends BaseService {
    public function __construct() {
        $dao = new CategoryDao();
        parent::__construct($dao);
    }
   
    /**
     * active categories for Add Anime 
     */
    public function get_active_categories() {
        return $this->dao->getAllActiveCategories();
    }
   
}