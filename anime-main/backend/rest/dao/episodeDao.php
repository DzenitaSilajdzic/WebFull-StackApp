<?php
require_once 'BaseDao.php';

class EpisodeDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("episodes");
    }

    /**
     * episodes of one anime for Watch Anime page 
     */
    public function getEpisodesByAnimeId($anime_id)
    {
        $query = "SELECT id, anime_id, season, episode_number, title, date_posted FROM episodes
                  WHERE anime_id = :anime_id AND status = 'aired'
                  ORDER BY season ASC, episode_number ASC";
        return $this->query($query, [':anime_id' => $anime_id]);
    }

    /**
     * first episode for Watch Anime
     */
    public function getFirstEpisodeByAnimeId($anime_id)
    {
        $query = "SELECT * FROM episodes
                  WHERE anime_id = :anime_id AND status = 'aired'
                  ORDER BY season ASC, episode_number ASC
                  LIMIT 1";
        return $this->query_unique($query, [':anime_id' => $anime_id]);
    }
   
    /**
     * new episode insert
     */
    public function addEpisode($episode)
    {
        return $this->add($episode);
    }
}