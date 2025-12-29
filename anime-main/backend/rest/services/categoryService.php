<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/categoryDao.php';

class categoryService extends baseService {
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