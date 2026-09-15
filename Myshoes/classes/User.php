<?php
/**
 * User - authentication, registration, and session helpers.
 */
require_once __DIR__ . '/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn()
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin($redirect = 'login.php')
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . $redirect);
            exit();
        }
    }

    public static function getId()
    {
        self::startSession();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function getUsername()
    {
        self::startSession();
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }

    public static function logout($redirect = 'index.php')
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();
        header('Location: ' . $redirect);
        exit();
    }

    /** @return array|null */
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare('SELECT * FROM Users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function usernameExists($username)
    {
        $stmt = $this->db->prepare('SELECT user_id FROM Users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare('SELECT user_id FROM Users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * @return true|string true on success, error message string on failure
     */
    public function login($username, $password)
    {
        $user = $this->findByUsername($username);
        if (!$user) {
            return 'User not found';
        }
        if (!password_verify($password, $user['password'])) {
            return 'Incorrect password';
        }

        self::startSession();
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['user_id'];
        return true;
    }

    /**
     * @return true|string true on success, error message string on failure
     */
    public function register($username, $email, $password)
    {
        if ($this->emailExists($email)) {
            return 'Email already exists';
        }
        if ($this->usernameExists($username)) {
            return 'Username already exists';
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO Users (username, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $email, $hashed);
        $ok = $stmt->execute();
        $error = $stmt->error;
        $stmt->close();

        return $ok ? true : ('Registration failed: ' . $error);
    }
}
