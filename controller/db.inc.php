<?php
class DatabaseConnection {
    private $pdo;

    public function __construct($db_host, $db_name, $db_user, $db_pass) {
        try {
            $this->pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // ✅ Quote function
    public function quote($value) {
        return $this->pdo->quote($value);
    }

    // ✅ Raw query execution
    public function query($sql) {
        return $this->pdo->query($sql);
    }

    // ✅ Prepared statement
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    // ✅ Last inserted ID
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // ✅ Transaction helpers
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
