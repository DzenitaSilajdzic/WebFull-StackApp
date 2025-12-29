<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/episodeDao.php';

class episodeService extends baseService {
    public function __construct() {
        $dao = new EpisodeDao();
        parent::__construct($dao);
    }

    /**
     * episodes for an anime 
     */
    public function get_episodes_by_anime($anime_id) {
        return $this->dao->getEpisodesByAnimeId($anime_id);
    }
   
    /**
     * first episode load
     */
    public function get_default_episode($anime_id) {
        return $this->dao->getFirstEpisodeByAnimeId($anime_id);
    }

    /**
     * add new episode to anime 
     */
    public function add_new_episode($data) {
        $episode_data = [
            'anime_id' => (int)$data['anime_id'],
            'season' => (int)$data['season'],
            'episode_number' => (int)$data['episode_number'],
            'title' => $data['title'],
            'video_url' => $data['video_url'],
            'duration' => $data['duration'] ?? 24,
            'date_posted' => date('Y-m-d H:i:s'),
            'status' => 'aired'
        ];
        return $this->dao->addEpisode($episode_data);
    }
}
