<?php
/**
 * Database - Singleton connection for the Myshoes project.
 * Usage: $conn = Database::getInstance()->getConnection();
 */
class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $this->conn = new mysqli('localhost', 'root', '', 'shoes');
        if ($this->conn->connect_errno !== 0) {
            die('Database Connection Error: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    /** Prevent cloning the singleton */
    private function __clone() {}
}
