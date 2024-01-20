<?php

class Likes extends Model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getLikesCountForThread($threadId) {
        $columns = 'COUNT(*) AS count';
        $table = 'likes';
        $where = 'thread_id = ?';
        $params = array($threadId);

        $sqlHelper = new SQLHelper($this->db);
        $result = $sqlHelper->executeSelect($table, $columns, $where, $params);

        if ($result !== false && !empty($result)) {
            return intval($result[0]['count']);
        } else {
            return 0;
        }
    }


    public function hasUserLikedThread($userId, $threadId) {
        $columns = 'COUNT(*) AS count';
        $table = 'likes';
        $where = 'user_id = ? AND thread_id = ?';
        $params = array($userId, $threadId);

        $sqlHelper = new SQLHelper($this->db);
        $result = $sqlHelper->executeSelect($table, $columns, $where, $params);

        if ($result !== false && !empty($result)) {
            return intval($result[0]['count']) > 0;
        } else {
            return false;
        }
    }

    public function likeThread($userId, $threadId) {
        // Check if the user has already liked the thread
        if ($this->hasUserLikedThread($userId, $threadId)) {
            // User has already liked the thread, you may choose to handle this scenario accordingly
            return false;
        }

        $data = array(
            'user_id' => $userId,
            'thread_id' => $threadId
        );

        $sqlHelper = new SQLHelper($this->db);
        $result = $sqlHelper->executeInsert('likes', $data);

        return $result;
    }


    public function toggleLikeForThread($userId, $threadId) {
        // Check if the user has already liked the thread
        if ($this->hasUserLikedThread($userId, $threadId)) {
            // User has already liked the thread, so unlike it
            return $this->unlikeThread($userId, $threadId);
        } else {
            // User has not liked the thread, so like it
            return $this->likeThread($userId, $threadId);
        }
    }

    private function unlikeThread($userId, $threadId) {
        $sqlHelper = new SQLHelper($this->db);

        $where = 'user_id = ? AND thread_id = ?';
        $params = array($userId, $threadId);

        // Delete the like record
        $result = $sqlHelper->executeDelete('likes', $where, $params);

        return $result;
    }
}
