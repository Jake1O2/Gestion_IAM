<?php
require_once __DIR__ . '/userDB.php';

class UserModel {
    protected $pdo;
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByEmail($email) {
        try{
            $stmt = $this->pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            // Log minimal info to file for debugging (do not expose details to users)
            @file_put_contents(__DIR__ . '/../logs/db_errors.log', date('c') . " - findByEmail error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    public function create($name, $email, $password, $role = 'student') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try{
            $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())');
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hash,
                'role' => $role
            ]);
        } catch (\PDOException $e) {
            @file_put_contents(__DIR__ . '/../logs/db_errors.log', date('c') . " - create user error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}

?>
