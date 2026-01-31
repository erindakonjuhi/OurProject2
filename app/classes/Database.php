<?php

class Database {
    private static $instance = null;
    private $connection;
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $db_name = 'moviereviewapp';

    private function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->db_name);
        
        if ($this->connection->connect_error) {
            die('Database Connection Failed: ' . $this->connection->connect_error);
        }
        
        $this->connection->set_charset('utf8mb4');
    }

  
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }

    public function __clone() {}
    public function __wakeup() {}
}
?>
