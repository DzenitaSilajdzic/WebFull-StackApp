<?php
require_once __DIR__ . '/baseService.php';
require_once __DIR__ . '/../dao/commentDao.php';

class commentService extends baseService {
    public function __construct() {
        $dao = new commentDao();
        parent::__construct($dao);
    }

    /**
     * active comments for an anime
     */
    public function get_anime_comments($anime_id) {
        return $this->dao->getCommentsByAnimeId($anime_id);
    }

    /**
     * add new comment
     */
    public function add_comment($data) {
        if (empty($data['user_id']) || empty($data['anime_id']) || empty($data['text'])) {
            throw new Exception("User ID, Anime ID, and text are required to post a comment.");
        }
       
        $comment = [
            'user_id' => (int)$data['user_id'],
            'anime_id' => (int)$data['anime_id'],
            'text' => $data['text'],
            'reply_id' => !empty($data['reply_id']) ? (int)$data['reply_id'] : null,
            'date_posted' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];
       
        return $this->dao->addComment($comment);
    }
   
    /**
     * HARD DELETE: Completely removes row from database
     */
    public function delete($id) {
        return $this->dao->delete($id);
    }

    public function remove_comment($id) {
        return $this->delete($id);
    }
}
?>