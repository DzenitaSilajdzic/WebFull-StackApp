<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AnimeDao.php';
require_once __DIR__ . '/../dao/EpisodeDao.php';

class AnimeService extends BaseService {
    private $anime_dao; 

    public function __construct()
    {
        $this->anime_dao = new AnimeDao(); 
        parent::__construct($this->anime_dao);
    }

    /**
     * anime for home/category
     */
    public function get_anime_listing($offset, $limit, $search) {
        
        $offset = (int) $offset;
        $limit = (int) $limit;
        
        return $this->anime_dao->getAnimeListing($offset, $limit, $search);
    }

    /**
     * anime for anime-detail
     */
    public function get_anime_details($anime_id) {
        $anime = $this->dao->getAnimeDetails($anime_id);
        if (!$anime) {
            throw new Exception("Anime not found.");
        }
        return $anime;
    }
   
    /**
     * add new anime, first episode
     */
    public function add_new_anime($data) {
        $anime_data = [
            'title' => $data['title'],
            'type' => $data['type'],
            'release_date' => $data['release_date'],
            'details' => $data['details'],
            'image_url' => $data['image_url'] ?? null,
            'status' => $data['status'] ?? 'airing',
        ];
       
        $new_anime = $this->dao->addAnime($anime_data);
        $anime_id = $new_anime['id'];

        // insert-anime_studio
        if (!empty($data['studio_id'])) {
            $studio_ids = is_array($data['studio_id']) ? $data['studio_id'] : [$data['studio_id']];
            foreach ($studio_ids as $studio_id) {
                 $this->dao->addAnimeStudio($anime_id, (int)$studio_id);
            }
        }
       
        // insert-anime_categories
        if (!empty($data['category_ids'])) {
            $category_ids = is_array($data['category_ids']) ? $data['category_ids'] : [$data['category_ids']];
            foreach ($category_ids as $category_id) {
                $this->dao->addAnimeCategory($anime_id, (int)$category_id);
            }
        }

        // insert-1.episode
        $episode_data = [
            'anime_id' => $anime_id,
            'season' => $data['episode_season'] ?? 1,
            'episode_number' => $data['episode_number'] ?? 1,
            'title' => $data['episode_title'] ?? $data['title'] . ' - Episode 1',
            'video_url' => $data['episode_video_url'],
            'duration' => $data['episode_duration'] ?? 24, // default 24min
            'date_posted' => date('Y-m-d H:i:s'),
            'status' => 'aired'
        ];
        $this->episode_dao->addEpisode($episode_data);

        return $new_anime;
    }
   
    /**
     * update status=deleted
     */
    public function remove_anime($anime_id) {
        return $this->dao->updateStatus($anime_id, 'deleted');
    }
}