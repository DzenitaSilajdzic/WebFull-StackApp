<?php
require_once 'BaseDao.php';

class AnimeDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("anime"); // Assuming your main table is named 'anime'
    }

    /**
     * anime home/category pages (WITHOUT RATING CALCULATION)
     */
   public function getAnimeListing($offset = 0, $limit = 10, $search = NULL) {

    // FIX 1: Explicitly cast to integer for safe SQL pagination
    $limit = (int) $limit;
    $offset = (int) $offset;

    // FIX 2: Removed AVG(c.rating) and the LEFT JOIN/GROUP BY clauses.
    $query = "
        SELECT a.* FROM anime a
    ";
    
    $params = [];
    $where_clauses = [];

    if ($search) {
        // Using WHERE since GROUP BY and HAVING were removed.
        $where_clauses[] = " a.name LIKE :search OR a.description LIKE :search ";
        $params['search'] = '%' . $search . '%';
    }
    
    // Add WHERE clause if search is present
    if (!empty($where_clauses)) {
        $query .= " WHERE " . implode(' AND ', $where_clauses);
    }
    
    // FIX 3: Inject the clean integer values for LIMIT and OFFSET
    $query .= " LIMIT " . $limit . " OFFSET " . $offset;

    return $this->query($query, $params); 
}

    /**
     * data for one anime
     */
    public function getAnimeDetails($anime_id)
    {
        // This query relies on categories and studios, NOT comments/ratings.
        $query = "
            SELECT
                a.*,
                GROUP_CONCAT(DISTINCT cat.name) AS genres,
                GROUP_CONCAT(DISTINCT s.name) AS studios
            FROM anime AS a
            LEFT JOIN anime_categories AS ac ON a.id = ac.anime_id
            LEFT JOIN categories AS cat ON ac.category_id = cat.id
            LEFT JOIN anime_studios AS ast ON a.id = ast.anime_id
            LEFT JOIN studios AS s ON ast.studio_id = s.id
            WHERE a.id = :id AND a.status != 'deleted'
            GROUP BY a.id
        ";
        return $this->query_unique($query, [':id' => $anime_id]);
    }
   
    /**
     * new anime insert
     */
    public function addAnime($anime)
    {
        return $this->add($anime);
    }
   
    /**
     * status update
     */
    public function updateStatus($id, $status)
    {
        return $this->update(['status' => $status], $id);
    }

    /**
     * for many-to-many with categories.
     */
    public function addAnimeCategory($anime_id, $category_id)
    {
        $junctionDao = new BaseDao('anime_categories');
        return $junctionDao->add(['anime_id' => $anime_id, 'category_id' => $category_id]);
    }

    /**
     * for many-to-many with studios.
     */
    public function addAnimeStudio($anime_id, $studio_id)
    {
        $junctionDao = new BaseDao('anime_studios');
        return $junctionDao->add(['anime_id' => $anime_id, 'studio_id' => $studio_id]);
    }
}
?>