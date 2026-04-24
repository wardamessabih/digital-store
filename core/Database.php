<?php

namespace Core;

require_once __DIR__ . '/Config.php';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        Config::load(dirname(__DIR__) . '/config.json');

        $config = Config::all();
        $dbConfig = $config['database'] ?? [];

        $host = $dbConfig['host'] ?? 'localhost';
        $name = $dbConfig['name'] ?? 'digital_store';
        $user = $dbConfig['user'] ?? 'root';
        $pass = $dbConfig['pass'] ?? '';
        $charset = $dbConfig['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        try {
            $this->connection = new \PDO($dsn, $user, $pass);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch (\PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            if (Config::get('app.debug', false)) {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('Database connection failed. Please try again later.');
        }
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

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch();
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchAll();
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        $params = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }

        $setString = implode(', ', $set);

        if (is_array($whereParams) && !empty($whereParams)) {
            $whereParts = [];
            foreach ($whereParams as $key => $value) {
                if (is_int($key)) {
                    $whereParts[] = $value;
                    $params['p' . count($params)] = $value;
                } else {
                    $whereParts[] = "{$key} = :where_{$key}";
                    $params["where_{$key}"] = $value;
                }
            }
            $where = implode(' AND ', $whereParts);
        }

        $sql = "UPDATE {$table} SET {$setString} WHERE {$where}";

        $this->query($sql, $params);

        return true;
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql, $params);
        return true;
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollBack();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
