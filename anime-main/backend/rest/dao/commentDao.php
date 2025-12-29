<?php
require_once __DIR__ . '/baseDao.php';

class commentDao extends baseDao
{
    public function __construct()
    {
        parent::__construct("comments");
    }

    /**
     * all comments with active status for one anime and user data
     */
    public function getCommentsByAnimeId($anime_id)
    {
        $query = "
            SELECT
                c.id, c.text, c.date_posted, c.reply_id, c.user_id,
                u.username, u.profile_img, u.role
            FROM comments AS c
            JOIN users AS u ON c.user_id = u.id
            WHERE c.anime_id = :anime_id AND c.status = 'active'
            ORDER BY c.date_posted DESC
        ";
        return $this->query($query, [':anime_id' => $anime_id]);
    }

    public function addComment($comment)
    {
        return $this->add($comment);
    }
   
    public function updateStatus($id, $status)
    {
        return $this->update(['status' => $status], $id);
    }
}