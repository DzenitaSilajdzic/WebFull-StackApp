<?php
require_once 'baseDao.php';

class animeDao extends baseDao
{
    public function __construct()
    {
        parent::__construct("anime");
    }

    public function getAnimeListing($offset = 0, $limit = 10, $search = NULL, $category_id = NULL) {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $query = "SELECT DISTINCT a.* FROM anime a";
        
        $params = [];
        $where_clauses = [];

        if ($category_id && $category_id != 'NULL') {
            $query .= " JOIN anime_categories ac ON a.id = ac.anime_id ";
            $where_clauses[] = " ac.category_id = :category_id ";
            $params['category_id'] = $category_id;
        }

        if ($search && $search != 'NULL') {
            $where_clauses[] = " (a.title LIKE :search OR a.description LIKE :search) ";
            $params['search'] = '%' . $search . '%';
        }
        
        $where_clauses[] = " a.status != 'deleted' ";

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $query .= " ORDER BY a.id DESC LIMIT " . $limit . " OFFSET " . $offset;

        return $this->query($query, $params); 
    }

    public function getAnimeDetails($anime_id)
    {
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
   
    public function addAnime($anime)
    {
        return $this->add($anime);
    }
   
    public function updateStatus($id, $status)
    {
        return $this->update(['status' => $status], $id);
    }

    public function addAnimeCategory($anime_id, $category_id)
    {
        $junctionDao = new BaseDao('anime_categories');
        return $junctionDao->add(['anime_id' => $anime_id, 'category_id' => $category_id]);
    }

    public function addAnimeStudio($anime_id, $studio_id)
    {
        $junctionDao = new BaseDao('anime_studios');
        return $junctionDao->add(['anime_id' => $anime_id, 'studio_id' => $studio_id]);
    }
}
?>