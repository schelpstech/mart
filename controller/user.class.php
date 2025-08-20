<?php

class User
{
    // Refer to database connection
    private $db;

    // Instantiate object with database connection
    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }

    // Register new user
    public function registerUser($data)
    {
        try {
            $sql = "INSERT INTO users (full_name, email, password, phone, role, status, date_registered) 
                    VALUES (:full_name, :email, :password, :phone, :role, :status, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":full_name", $data['full_name']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":password", password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindParam(":phone", $data['phone']);
            $stmt->bindParam(":role", $data['role']);
            $stmt->bindParam(":status", $data['status']);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Authenticate login
    public function loginUser($email, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND status = 'active'");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['active'] = $user['email'];
                $_SESSION['user_id'] = $user['id'];
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get user by ID
    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all users
    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update user
    public function updateUser($id, $data)
    {
        $sql = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, role = :role, status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":full_name", $data['full_name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":role", $data['role']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Delete user
    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Check if email exists
    public function isEmailExists($email)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Record log (already added by you)
    public function recordLog($object, $activity, $description)
    {
        try {
            $sql = "INSERT INTO log (user_name, uip, object, activity, description) 
                    VALUES(:user, :userip, :object, :activity, :description)";
            $query = $this->db->prepare($sql);

            $user = (isset($_SESSION['activeAdmin'])) ? $_SESSION['activeAdmin'] : ($_SESSION['active'] ?? 'unknown');
            $query->bindParam(":user", $user);
            $query->bindParam(":userip", $_SERVER['REMOTE_ADDR']);
            $query->bindParam(":object", $object);
            $query->bindParam(":activity", $activity);
            $query->bindParam(":description", $description);

            $query->execute();
        } catch (PDOException $e) {
            // log or ignore error
        }
    }

    // Logout
    public function logout()
    {
        session_destroy();
        unset($_SESSION['active']);
        unset($_SESSION['user_id']);
        return true;
    }
}
