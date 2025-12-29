<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/animeDao.php';
require_once __DIR__ . '/../dao/episodeDao.php';

class animeService extends baseService {
    private $anime_dao; 
    private $episode_dao; 

    public function __construct()
    {
        $this->anime_dao = new AnimeDao(); 
        $this->episode_dao = new EpisodeDao();
        parent::__construct($this->anime_dao);
    }

    /**
     * Get listing for home page
     */
    public function get_anime_listing($offset, $limit, $search, $category_id = NULL) {
        return $this->anime_dao->getAnimeListing($offset, $limit, $search, $category_id);
    }

    /**
     * Get details for anime-details page
     */
    public function get_anime_details($anime_id) {
        $anime = $this->anime_dao->getAnimeDetails($anime_id);
        if (!$anime) {
            throw new Exception("Anime not found.");
        }
        return $anime;
    }
   
    /**
     * Add new anime and first episode
     */
    public function add_new_anime($data) {
        $anime_data = [
            'title' => $data['title'],
            'type' => $data['type'],
            'release_date' => $data['release_date'],
            'description' => $data['description'] ?? $data['details'],
            'image_url' => $data['image_url'] ?? null,
            'status' => $data['status'] ?? 'airing',
        ];
       
        $new_anime = $this->anime_dao->addAnime($anime_data);
        $anime_id = $new_anime['id'];

        if (!empty($data['studio_id'])) {
            $studio_ids = is_array($data['studio_id']) ? $data['studio_id'] : [$data['studio_id']];
            foreach ($studio_ids as $studio_id) {
                 $this->anime_dao->addAnimeStudio($anime_id, (int)$studio_id);
            }
        }
       
        if (!empty($data['category_ids'])) {
            $category_ids = is_array($data['category_ids']) ? $data['category_ids'] : [$data['category_ids']];
            foreach ($category_ids as $category_id) {
                $this->anime_dao->addAnimeCategory($anime_id, (int)$category_id);
            }
        }

        $episode_data = [
            'anime_id' => $anime_id,
            'season' => $data['episode_season'] ?? 1,
            'episode_number' => $data['episode_number'] ?? 1,
            'title' => $data['episode_title'] ?? $data['title'] . ' - Episode 1',
            'video_url' => $data['episode_video_url'], 
            'duration' => $data['episode_duration'] ?? 24,
            'date_posted' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->episode_dao->add($episode_data); 

        return $new_anime;
    }
   
    public function remove_anime($anime_id) {
        return $this->anime_dao->updateStatus($anime_id, 'deleted');
    }

    public function update_anime($id, $data) {
        return $this->anime_dao->update($data, $id);
    }
}
?>