<?php

class User extends Model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createUser($username, $email, $password) {
        // Check if the user already exists
        if ($this->userExists($username, $email) == 1) {
            return 2;
        }

        // Encrypt sensitive data
        $encryptedUsername = $this->encrypt($username);
        $encryptedEmail = $this->encrypt($email);
        $encryptedPassword = $this->encrypt($password);

        // Insert into the database
        $data = array(
            'username' => $encryptedUsername,
            'email' => $encryptedEmail,
            'password' => $encryptedPassword
        );

        $sqlHelper = new SQLHelper($this->db);
        $result = $sqlHelper->executeInsert('users', $data);

        if ($result) {
            return 1;
        } else {
            echo "Error creating user.";
        }
    }

    private function userExists($username, $email) {
        $sqlHelper = new SQLHelper($this->db);
        $columns = 'id,username,email';

        $allUsers = $sqlHelper->executeSelect('users', $columns);

        $userExists = 0;

        foreach ($allUsers as $user) {
            $name = $this->decrypt($user['username']);
            $useremail = $this->decrypt($user['email']);
            if ($name == $username and $useremail == $email) {
                $userExists =  1;
                break;
                exit;
            }
            $userExists = 0;
        }

        return $userExists;
    }

    public function getUser($userId) {
        // Retrieve user data from the database
        $columns = 'id, username, email, password';
        $where = 'id = ?';
        $params = array($userId);

        $sqlHelper = new SQLHelper($this->db);
        $userData = $sqlHelper->executeSelect('users', $columns, $where, $params);

        if (!empty($userData)) {
            $row = $userData[0];

            // Decrypt sensitive data
            $decryptedUsername = $this->decrypt($row['username']);
            $decryptedEmail = $this->decrypt($row['email']);
            $decryptedPassword = $this->decrypt($row['password']);

            // Return user data
            return [
                'id' => $row['id'],
                'username' => $decryptedUsername,
                'email' => $decryptedEmail,
                'password' => $decryptedPassword
            ];
        } else {
            return null;
        }
    }

    public function getAllUsers()
    {
        // Retrieve all users from the database
        $columns = 'id, username, email, profile_image';
        $sqlHelper = new SQLHelper($this->db);
        $allUsers = $sqlHelper->executeSelect('users', $columns);

        // Decrypt and return user data
        $decryptedUsers = array();
        foreach ($allUsers as $user) {
            $decryptedUsers[] = [
                'id' => $user['id'],
                'username' => $this->decrypt($user['username']),
                'email' => $this->decrypt($user['email']),
                'profile_image' => $this->decrypt($user['profile_image'])
            ];
        }

        return $decryptedUsers;
    }

    public function authenticateUser($emailOrUsername, $password) {
        $sqlHelper = new SQLHelper($this->db);
        // Fetch all users from the database
        $allUsers = $sqlHelper->executeSelect('users', 'id, username, email, password, profile_image');

        // Loop through all users to find a match
        foreach ($allUsers as $user) {
            // Decrypt the password from the database
            $storedPassword = $this->decrypt($user['password']);

            // Check if the provided value matches the username or email
            if (($emailOrUsername === $this->decrypt($user['username']) or $emailOrUsername === $this->decrypt($user['email'])) and $storedPassword === $password) {
                return $user;
            }
        }

        // Invalid email/username or password
        return null;
    }

}
