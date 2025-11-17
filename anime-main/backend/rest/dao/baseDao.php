<?php
require_once __DIR__ . "/../config.php";

class BaseDao
{
    protected $connection;
    private $table_name;

    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        try {
            $this->connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . ";port=" . Config::DB_PORT(),
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * queries that return multiple rows
     */
    protected function query($query, $params = [])
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * queries that return a single row
     */
    protected function query_unique($query, $params = [])
    {
        $results = $this->query($query, $params);
        return reset($results);
    }

    /**
     * method fetch all
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM " . $this->table_name, []);
    }

    /**
     * method fetch by id
     */
    public function getById($id, $id_column = "id")
    {
        return $this->query_unique("SELECT * FROM " . $this->table_name . " WHERE " . $id_column . " = :id", [':id' => $id]);
    }

    /**
     * insert method
     */
    public function add($entity)
    {
        $columns = implode(', ', array_keys($entity));
        $placeholders = ':' . implode(', :', array_keys($entity));

        $query = "INSERT INTO " . $this->table_name . " ({$columns}) VALUES ({$placeholders})";
       
        $stmt = $this->connection->prepare($query);
        $stmt->execute($entity);
       
        $entity['id'] = $this->connection->lastInsertId();
        return $entity;
    }

    /**
     * update method
     */
    public function update($entity, $id, $id_column = "id")
    {
        $set_clause = [];
        foreach ($entity as $column => $value) {
            $set_clause[] = $column . "=:" . $column;
        }
        $set_clause_str = implode(', ', $set_clause);

        $query = "UPDATE " . $this->table_name . " SET " . $set_clause_str . " WHERE " . $id_column . " = :id";
       
        $stmt = $this->connection->prepare($query);
        $entity['id'] = $id; // id for array binding
        $stmt->execute($entity);
        return $entity;
    }

    /**
     * delete method
     */
    public function delete($id, $id_column = "id")
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE " . $id_column . " = :id";
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}