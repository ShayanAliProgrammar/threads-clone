<?php

class Threads extends Model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createThread($userId, $content) {
        $data = array(
            'user_id' => $userId,
            'content' => $content
        );

        $sqlHelper = new SQLHelper($this->db);
        $result = $sqlHelper->executeInsert('threads', $data);

        if ($result) {
            $lastThreadId = $this->getLastInsertedThreadId($this->db);
            return $lastThreadId;
        } else {
            return 0;
        }
    }

    private function getLastInsertedThreadId($mysqli) {
        return $mysqli->insert_id;
    }

    public function getThread($threadId, $userId = null) {
        $columns = 'threads.id, threads.user_id, threads.content, threads.date_created, users.username, users.profile_image';
        $joins = 'INNER JOIN users ON threads.user_id = users.id';
        $where = 'threads.id = ?';
        $params = array($threadId);

        $sqlHelper = new SQLHelper($this->db);
        $threadData = $sqlHelper->executeSelect('threads', $columns, $where, $params, $joins);

        if (!empty($threadData)) {
            $row = $threadData[0];

            // Fetch comments for the thread
            $comments = (new Comments($this->db))->getCommentsForThread($threadId);

            // Fetch likes count and check if the user has liked the thread
            $likesCount = (new Likes($this->db))->getLikesCountForThread($threadId);
            $userLiked = ($userId !== null) ? (new Likes($this->db))->hasUserLikedThread($userId, $threadId) : false;

            return [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'username' => (new Model())->decrypt($row['username']),
                'content' => $row['content'],
                'profile_image' => (new Model())->decrypt($row['profile_image']),
                'date' => $row['date_created'],
                'comments' => $comments,
                'likes_count' => $likesCount,
                'user_liked' => $userLiked,
            ];
        } else {
            return null;
        }
    }


    public function getPaginatedThreads($limit, $offset, $userId = null) {
        $columns = 'threads.id, threads.user_id, threads.content, threads.date_created, users.username, users.profile_image';
        $joins = 'INNER JOIN users ON threads.user_id = users.id';
        $sqlHelper = new SQLHelper($this->db);
        $where = null;

        // Execute the query using the executeSelect method with LIMIT and OFFSET
        $data = $sqlHelper->executeSelect('threads', $columns, $where, null, $joins, "threads.date_created DESC", $limit, $offset);

        $result = array();
        foreach ($data as $thread) {
            $comments = (new Comments($this->db))->getCommentsCountForThread($thread['id']);

            // Fetch likes count and check if the user has liked the thread
            $likesCount = (new Likes($this->db))->getLikesCountForThread($thread['id']);
            $userLiked = ($userId !== null) ? (new Likes($this->db))->hasUserLikedThread($userId, $thread['id']) : false;

            $result[] = [
                'id' => $thread['id'],
                'user_id' => $thread['user_id'],
                'username' => (new Model())->decrypt($thread['username']),
                'content' => $thread['content'],
                'comments' => $comments,
                'profile_image' => (new Model())->decrypt($thread['profile_image']),
                'date' => $thread['date_created'],
                'likes_count' => $likesCount,
                'user_liked' => $userLiked,
            ];
        }

        return $result;
    }

    public function searchThreads($limit, $offset, $search, $userId = null) {
        $columns = 'threads.id, threads.user_id, threads.content, threads.date_created, users.username, users.profile_image';
        $joins = 'INNER JOIN users ON threads.user_id = users.id';
        $sqlHelper = new SQLHelper($this->db);
        $where = null;

        // Execute the query using the executeSelect method with LIMIT and OFFSET
        $data = $sqlHelper->executeSelect('threads', $columns, $where, null, $joins, "threads.date_created DESC", $limit, $offset);

        $result = array();
        foreach ($data as $thread) {
            $comments = (new Comments($this->db))->getCommentsCountForThread($thread['id']);

            // Fetch likes count and check if the user has liked the thread
            $likesCount = (new Likes($this->db))->getLikesCountForThread($thread['id']);
            $userLiked = ($userId !== null) ? (new Likes($this->db))->hasUserLikedThread($userId, $thread['id']) : false;

            if (str_contains(strtolower((new Model())->decrypt($thread['username'])), strtolower($search)) or str_contains(strtolower((new Model())->decrypt($thread['profile_image'])), strtolower($search)) or str_contains(strtolower($thread['content']), strtolower($search))) {
                $result[] = [
                    'id' => $thread['id'],
                    'user_id' => $thread['user_id'],
                    'username' => (new Model())->decrypt($thread['username']),
                    'content' => $thread['content'],
                    'comments' => $comments,
                    'profile_image' => (new Model())->decrypt($thread['profile_image']),
                    'date' => $thread['date_created'],
                    'likes_count' => $likesCount,
                    'user_liked' => $userLiked,
                ];
            }
        }

        return $result;
    }

    public function getTotalThreadCount() {
        $sqlHelper = new SQLHelper($this->db);
        $count = $sqlHelper->executeScalarCount('threads');

        return intval($count);
    }

    public function getTotalThreadCountBySearch($limit, $offset, $search, $userId = null) {
        $i = 0;
        $columns = 'threads.id, threads.user_id, threads.content, threads.date_created, users.username, users.profile_image';
        $joins = 'INNER JOIN users ON threads.user_id = users.id';
        $sqlHelper = new SQLHelper($this->db);
        $where = null;

        // Execute the query using the executeSelect method with LIMIT and OFFSET
        $data = $sqlHelper->executeSelect('threads', $columns, $where, null, $joins, "threads.date_created DESC", $limit, $offset);

        $result = array();
        foreach ($data as $thread) {
            $comments = (new Comments($this->db))->getCommentsCountForThread($thread['id']);

            // Fetch likes count and check if the user has liked the thread
            $likesCount = (new Likes($this->db))->getLikesCountForThread($thread['id']);
            $userLiked = ($userId !== null) ? (new Likes($this->db))->hasUserLikedThread($userId, $thread['id']) : false;

            $i++;

            if (strpos((new Model())->decrypt($thread['username']), $search) and strpos((new Model())->decrypt($thread['profile_image']), $search) and strpos($thread['content'], $search)) {
                $result[] = [
                    'id' => $thread['id'],
                    'user_id' => $thread['user_id'],
                    'username' => (new Model())->decrypt($thread['username']),
                    'content' => $thread['content'],
                    'comments' => $comments,
                    'profile_image' => (new Model())->decrypt($thread['profile_image']),
                    'date' => $thread['date_created'],
                    'likes_count' => $likesCount,
                    'user_liked' => $userLiked,
                ];
            }
        }

        return $i;
    }


    public function getAllThreads($status = 'latest') {
        $columns = 'threads.id, threads.user_id, threads.content, threads.date_created, users.username, users.profile_image';
        $joins = 'INNER JOIN users ON threads.user_id = users.id';
        $sqlHelper = new SQLHelper($this->db);
        $where = null;
        $orderBy = ($status === 'oldest') ? 'threads.date_created ASC' : 'threads.date_created DESC';

        // Execute the query using the executeSelect method
        $data = $sqlHelper->executeSelect('threads', $columns, $where, null, $joins, $orderBy);

        $result = array();
        foreach ($data as $thread) {
            $result[] = [
                'id' => $thread['id'],
                'user_id' => $thread['user_id'],
                'username' => (new Model())->decrypt($thread['username']),
                'content' => $thread['content'],
                'profile_image' => (new Model())->decrypt($thread['profile_image']),
                'date' => $thread['date_created'],
            ];
        }

        return $result;
    }
}
