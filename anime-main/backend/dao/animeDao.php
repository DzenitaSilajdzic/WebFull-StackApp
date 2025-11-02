<?php
require_once 'BaseDao.php';

class AnimeDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("anime");
    }

    /**
     * anime home/category pages
     */
    public function getAnimeListing($offset = 0, $limit = 10, $category_id = null)
    {
        $query = "
            SELECT
                a.id, a.title, a.image_url, a.type, a.status, a.popularity AS total_views,
                COUNT(DISTINCT c.id) AS total_comments,
                COUNT(DISTINCT e.id) AS total_episodes,
                (SELECT COUNT(id) FROM episodes WHERE anime_id = a.id AND status = 'aired') AS episodes_aired
            FROM anime AS a
            LEFT JOIN comments AS c ON a.id = c.anime_id AND c.status = 'active'
            LEFT JOIN anime_categories AS ac ON a.id = ac.anime_id
            WHERE a.status != 'deleted'
        ";
        $params = [];
       
        if ($category_id) {
            $query .= " AND ac.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
       
        $query .= " GROUP BY a.id, a.title, a.image_url, a.type, a.status, a.popularity
                    ORDER BY a.release_date DESC
                    LIMIT :limit OFFSET :offset";
       
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        return $this->query($query, $params);
    }

    /**
     * data for one anime
     */
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