<?php
class Model {
    private $db;

    public function __construct($db_conn) {
        $this->db = $db_conn;
    }

    public function insert($table, $fields) {
        $columns = implode(", ", array_keys($fields));
        $placeholders = ":" . implode(", :", array_keys($fields));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        foreach ($fields as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

   public function update($table, $fields, $condition = "1 = 1") {
    $setClause = [];
    foreach ($fields as $key => $value) {
        $setClause[] = "$key = " . $this->db->quote($value);
    }

    // Handle condition
    if (is_array($condition)) {
        $whereParts = [];
        foreach ($condition as $key => $value) {
            $whereParts[] = "$key = " . $this->db->quote($value);
        }
        $condition = implode(" AND ", $whereParts);
    }

    $sql = "UPDATE {$table} SET " . implode(", ", $setClause) . " WHERE {$condition}";
    return $this->db->exec($sql);
}


   public function delete($table, $condition = "1 = 1") {
    // Handle array condition
    if (is_array($condition)) {
        $whereParts = [];
        foreach ($condition as $key => $value) {
            $whereParts[] = "$key = " . $this->db->quote($value);
        }
        $condition = implode(" AND ", $whereParts);
    }

    $sql = "DELETE FROM {$table} WHERE {$condition}";
    return $this->db->exec($sql);
}


    public function getRows($table, $conditions = array()) {
        $sql = 'SELECT ';
        $sql .= array_key_exists("select", $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM ' . $table;

        if (array_key_exists("join", $conditions)) {
            $sql .= ' INNER JOIN ' . $conditions['join'];
        }
        if (array_key_exists("leftjoin", $conditions)) {
            $sql .= ' LEFT JOIN ' . $conditions['leftjoin'];
        }
        if (array_key_exists("joinx", $conditions)) {
            foreach ($conditions['joinx'] as $key => $value) {
                $sql .= ' INNER JOIN ' . $key . $value;
            }
        }
        if (array_key_exists("joinl", $conditions)) {
            foreach ($conditions['joinl'] as $key => $value) {
                $sql .= ' LEFT JOIN ' . $key . $value;
            }
        }

        $whereClauses = [];
        if (array_key_exists("where", $conditions)) {
            foreach ($conditions['where'] as $key => $value) {
                if (is_array($value)) {
                    $placeholders = implode(',', array_map(fn($v) => $this->db->quote($v), $value));
                    $whereClauses[] = "$key IN ($placeholders)";
                } else {
                    $whereClauses[] = "$key = " . $this->db->quote($value);
                }
            }
        }

        if (array_key_exists("where_raw", $conditions)) {
            $whereClauses[] = $conditions['where_raw'];
        }

        if (!empty($whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        if (array_key_exists("where_not", $conditions)) {
            foreach ($conditions['where_not'] as $key => $value) {
                $sql .= " AND $key != " . $this->db->quote($value);
            }
        }
        if (array_key_exists("where_greater_equals", $conditions)) {
            foreach ($conditions['where_greater_equals'] as $key => $value) {
                $sql .= " AND $key >= " . $this->db->quote($value);
            }
        }
        if (array_key_exists("where_lesser_equals", $conditions)) {
            foreach ($conditions['where_lesser_equals'] as $key => $value) {
                $sql .= " AND $key <= " . $this->db->quote($value);
            }
        }
        if (array_key_exists("where_lesser", $conditions)) {
            foreach ($conditions['where_lesser'] as $key => $value) {
                $sql .= " AND $key < " . $this->db->quote($value);
            }
        }
        if (array_key_exists("where_greater", $conditions)) {
            foreach ($conditions['where_greater'] as $key => $value) {
                $sql .= " AND $key > " . $this->db->quote($value);
            }
        }

        if (array_key_exists("group_by", $conditions)) {
            $sql .= ' GROUP BY ' . $conditions['group_by'];
        }
        if (array_key_exists("order_by", $conditions)) {
            $sql .= ' ORDER BY ' . $conditions['order_by'];
        }
        if (array_key_exists("limit", $conditions)) {
            if (array_key_exists("start", $conditions)) {
                $sql .= ' LIMIT ' . $conditions['start'] . ', ' . $conditions['limit'];
            } else {
                $sql .= ' LIMIT ' . $conditions['limit'];
            }
        }

        $query = $this->db->prepare($sql);
        $query->execute();

        if (array_key_exists("return_type", $conditions) && $conditions['return_type'] != 'all') {
            switch ($conditions['return_type']) {
                case 'count':
                    return $query->rowCount();
                case 'single':
                    return $query->fetch(PDO::FETCH_ASSOC);
                default:
                    return false;
            }
        } else {
            return $query->rowCount() > 0 ? $query->fetchAll(PDO::FETCH_ASSOC) : false;
        }
    }

    public function getById($table, $id) {
        $sql = "SELECT * FROM {$table} WHERE id = " . $this->db->quote($id);
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function exists($table, $condition = "1 = 1") {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$condition}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn() > 0;
    }

    public function lastInsertId()
{
    return $this->db->lastInsertId();
}


    public function countRows($table, $condition = "1 = 1") {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$condition}";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function search($table, $columns, $searchTerm) {
        $conditions = [];
        foreach ($columns as $col) {
            $conditions[] = "$col LIKE " . $this->db->quote("%{$searchTerm}%");
        }
        $sql = "SELECT * FROM {$table} WHERE " . implode(" OR ", $conditions);
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sum($table, $column, $condition = "1 = 1") {
        $sql = "SELECT SUM({$column}) AS total FROM {$table} WHERE {$condition}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollBack() {
        return $this->db->rollBack();
    }
}
