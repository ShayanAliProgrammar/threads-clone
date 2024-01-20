<?php


class Model {
    private $privateKey = 'model-secret';
    private const ENCRYPTION_METHOD = 'AES-256-CBC';

    public function __construct($privateKey = null) {
        $this->privateKey = $privateKey ?? 'model-secret';
    }

    public function encrypt($data) {
        $ivSize = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encryptedData = openssl_encrypt($data, self::ENCRYPTION_METHOD, $this->privateKey, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encryptedData);
    }

    public function decrypt($data) {
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv = substr($data, 0, $ivSize);
        $encryptedData = substr($data, $ivSize);

        return openssl_decrypt($encryptedData, self::ENCRYPTION_METHOD, $this->privateKey, OPENSSL_RAW_DATA, $iv);
    }
}

class SQLHelper {
    private $db;

    public function __construct($database = null) {
        global $db;

        $this->db = $database ?? $db;
    }

    public function executeInsert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));

        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        $stmt = $this->prepareAndBind($query, $data);

        if (!$stmt) {
            return false;
        }

        // Execute the query
        $result = $stmt->execute();

        // Close the statement
        $stmt->close();

        return $result;
    }


    public function executeScalarCount($table, $where = null, $params = array(), $joins = "") {
        $query = "SELECT COUNT(*) FROM $table";

        // Add joins if provided
        if (!empty($joins)) {
            $query .= " $joins";
        }

        if ($where !== null) {
            $query .= " WHERE $where";
        }

        $stmt = $this->prepareAndBind($query, $params);

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

    public function executeDelete($table, $where, $params) {
        $query = "DELETE FROM $table WHERE $where";
        $stmt = $this->prepareAndBind($query, $params);

        if (!$stmt) {
            return false;
        }

        // Execute the query
        $result = $stmt->execute();

        // Close the statement
        $stmt->close();

        return $result;
    }

    public function executeUpdate($table, $data, $where, $params) {
        $setClause = '';
        foreach ($data as $column => $value) {
            $setClause .= "$column = ?, ";
        }
        $setClause = rtrim($setClause, ', ');

        $query = "UPDATE $table SET $setClause WHERE $where";
        $stmt = $this->prepareAndBind($query, array_merge(array_values($data), $params));

        if (!$stmt) {
            return false;
        }

        // Execute the query
        $result = $stmt->execute();

        // Close the statement
        $stmt->close();

        return $result;
    }

    public function executeSelect($table, $columns = "*", $where = null, $params = array(), $joins = "", $orderBy = "", $limit = null, $offset = null) {
        $query = "SELECT $columns FROM $table";

        // Add joins if provided
        if (!empty($joins)) {
            $query .= " $joins";
        }

        if ($where !== null) {
            $query .= " WHERE $where";
        }

        // Add ORDER BY clause if provided
        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy";
        }

        // Add LIMIT and OFFSET if provided
        if ($limit !== null) {
            $query .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset !== null) {
            $query .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->prepareAndBind($query, $params);

        if (!$stmt) {
            return false;
        }

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch data
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Close the statement
        $stmt->close();

        return $data;
    }


    public function prepareAndBind($query, $params) {
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            echo "Error in the prepare statement: " . $this->db->error;
            return false;
        }

        // Bind parameters
        $this->bindParams($stmt, $params);

        return $stmt;
    }

    public function bindParams($stmt, $params) {
        if (!empty($params)) {
            $paramTypes = str_repeat("s", count($params)); // Assuming all parameters are strings

            $bindParams = array(&$paramTypes);
            foreach ($params as $key => $value) {
                $bindParams[] = &$params[$key];
            }

            call_user_func_array(array($stmt, 'bind_param'), $bindParams);
        }
    }
}
