<?php
class Model
{
    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }
    // Safely escape and quote values
    public function quote($value)
    {
        return $this->db->quote($value);
    }
    public function prepare($sql)
    {
        return $this->db->prepare($sql);
    }
    /**
     * Insert a record
     */
    public function insert($table, $fields)
    {
        $columns = implode(", ", array_keys($fields));
        $placeholders = ":" . implode(", :", array_keys($fields));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        foreach ($fields as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Update records
     */
    public function update($table, $fields, $condition = "1=1")
    {
        $setClause = [];
        $params = [];

        foreach ($fields as $key => $value) {
            $paramKey = ":set_" . $key;
            $setClause[] = "$key = $paramKey";
            $params[$paramKey] = $value; // bind value later
        }

        // Handle condition if array
        if (is_array($condition)) {
            $whereParts = [];
            foreach ($condition as $key => $value) {
                $paramKey = ":where_" . $key;
                $whereParts[] = "$key = $paramKey";
                $params[$paramKey] = $value;
            }
            $conditionSql = implode(" AND ", $whereParts);
        } else {
            $conditionSql = $condition; // raw string
        }

        $sql = "UPDATE {$table} SET " . implode(", ", $setClause) . " WHERE {$conditionSql}";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val);
        }

        return $stmt->execute();
    }


    /**
     * Delete records
     */
    public function delete($table, $condition = "1=1")
    {
        $params = [];

        if (is_array($condition)) {
            $whereParts = [];
            foreach ($condition as $key => $value) {
                $paramKey = ":where_" . $key;
                $whereParts[] = "$key = $paramKey";
                $params[$paramKey] = $value; // bind value later
            }
            $conditionSql = implode(" AND ", $whereParts);
        } else {
            $conditionSql = $condition; // raw string condition
        }

        $sql = "DELETE FROM {$table} WHERE {$conditionSql}";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val);
        }

        return $stmt->execute();
    }


    /**
     * Universal query builder
     */
    public function getRows($table, $params = [])
{
    $sql = "SELECT ";
    $sql .= $params['select'] ?? "*";
    $sql .= " FROM {$table}";

    // Handle joins
    foreach (['join', 'left_join', 'right_join', 'full_join'] as $joinType) {
        if (!empty($params[$joinType]) && is_array($params[$joinType])) {
            foreach ($params[$joinType] as $joinTable => $onClause) {
                $type = strtoupper(str_replace('_', ' ', $joinType));
                $sql .= " {$type} {$joinTable} {$onClause}";
            }
        }
    }

    // WHERE conditions
    $bindings = [];
    $whereClauses = [];

    if (!empty($params['where']) && is_array($params['where'])) {
        foreach ($params['where'] as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $i => $val) {
                    $ph = ":{$key}_{$i}";
                    $placeholders[] = $ph;
                    $bindings[$ph] = $val;
                }
                $whereClauses[] = "{$key} IN (" . implode(",", $placeholders) . ")";
            } else {
                $ph = ":{$key}";
                $whereClauses[] = "{$key} = {$ph}";
                $bindings[$ph] = $value;
            }
        }
    }

    // Raw WHERE
    if (!empty($params['where_raw'])) {
        $whereClauses[] = $params['where_raw'];
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // GROUP BY
    if (!empty($params['group_by'])) {
        $sql .= " GROUP BY " . $params['group_by'];
    }

    // HAVING
    if (!empty($params['having'])) {
        $sql .= " HAVING " . $params['having'];
    }

    // ORDER BY
    if (!empty($params['order_by'])) {
        $sql .= " ORDER BY " . $params['order_by'];
    }

    // LIMIT and OFFSET
    if (!empty($params['limit'])) {
        $sql .= " LIMIT " . (int)$params['limit'];
        if (!empty($params['offset'])) {
            $sql .= " OFFSET " . (int)$params['offset'];
        }
    }

    // Prepare and execute
    $stmt = $this->db->prepare($sql);
    $stmt->execute($bindings);

    // Return type
    $returnType = $params['return_type'] ?? 'all';

    switch ($returnType) {
        case 'single':
            return $stmt->fetch(PDO::FETCH_ASSOC);
        case 'count':
            return $stmt->rowCount();
        case 'sum':
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        case 'all':
        default:
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


    public function getById($table, $id)
    {
        $sql = "SELECT * FROM {$table} WHERE id = " . $this->db->quote($id);
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function exists($table, $condition = "1=1")
    {
        if (is_array($condition)) {
            $clauses = [];
            $params = [];
            foreach ($condition as $col => $val) {
                $clauses[] = "{$col} = ?";
                $params[] = $val;
            }
            $where = implode(" AND ", $clauses);
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        } else {
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$condition}";
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchColumn() > 0;
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function countRows($table, $condition = "1=1")
    {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$condition}";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function search($table, $columns, $searchTerm)
    {
        $conditions = [];
        foreach ($columns as $col) {
            $conditions[] = "$col LIKE " . $this->db->quote("%{$searchTerm}%");
        }
        $sql = "SELECT * FROM {$table} WHERE " . implode(" OR ", $conditions);
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sum($table, $column, $condition = "1=1")
    {
        $sql = "SELECT SUM({$column}) AS total FROM {$table} WHERE {$condition}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Transaction helpers
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    public function commit()
    {
        return $this->db->commit();
    }
    public function rollBack()
    {
        return $this->db->rollBack();
    }
}
