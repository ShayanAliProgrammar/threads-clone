<?php

class Comments extends Model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

     public function addComment($userId, $threadId, $content) {
        // Insert into the comments table
        $data = array(
            'user_id' => $userId,
            'thread_id' => $threadId,
            'comment' => $content,
        );

        $sqlHelper = new SQLHelper($this->db);
        $result = $sqlHelper->executeInsert('comments', $data);

        return $result;
    }

    private function getUserDetails($userId) {
        $columns = 'username, profile_image';
        $where = 'id = ?';
        $params = array($userId);

        $sqlHelper = new SQLHelper($this->db);
        $userData = $sqlHelper->executeSelect('users', $columns, $where, $params);

        if (!empty($userData)) {
            $row = $userData[0];
            return [
                'username' => $row['username'],
                'profile_image' => $row['profile_image']
            ];
        } else {
            return null;
        }
    }

    public function getCommentsForThread($threadId, $limit = null, $offset = null) {
        $columns = 'id, user_id, comment, date_commented';
        $where = 'thread_id = ?';
        $params = array($threadId);

        $sqlHelper = new SQLHelper($this->db);
        $commentsData = $sqlHelper->executeSelect('comments', $columns, $where, $params, '', 'id DESC', $limit, $offset);

        $comments = array();
        foreach ($commentsData as $comment) {
            // Fetch user details for each comment
            $userDetails = $this->getUserDetails($comment['user_id']);

            $comments[] = [
                'id' => $comment['id'],
                'user_id' => $comment['user_id'],
                'username' => (new Model())->decrypt($userDetails['username']),
                'profile_image' => (new Model())->decrypt($userDetails['profile_image']),
                'content' => $comment['comment'],
                'date' => $comment['date_commented']
            ];
        }

        return $comments;
    }

    public function getCommentsCountForThread($threadId) {
        $query = "SELECT COUNT(*) FROM comments WHERE thread_id = ?";
        $params = array($threadId);

        $stmt = (new SQLHelper())->prepareAndBind($query, $params);

        if (!$stmt) {
            return false;
        }

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch the first row and first column value
        $count = $result->fetch_array(MYSQLI_NUM)[0];

        // Close the statement
        $stmt->close();

        return $count;
    }

    public function getTotalCommentsForThread($threadId) {
        $where = 'thread_id = ?';
        $params = array($threadId);

        $sqlHelper = new SQLHelper($this->db);
        $count = $sqlHelper->executeScalarCount('comments', $where, $params);

        return intval($count);
    }

    public function getTotalCommentsForCommentId($commentId) {
        $where = 'thread_id = ?';
        $params = array($commentId);

        $sqlHelper = new SQLHelper($this->db);
        $count = $sqlHelper->executeScalarCount('comments', $where, $params);

        return intval($count);
    }
}
