<?php

    namespace App;

    class Database
    {
        private $connection = null;

        /** @var Config $config */
        private $config = null;

        private static $instance = null;

        private function __construct(Config $config)
        {
            $this->config = $config;
            $this->connection = $this->getConnection();
        }

        private function getConnection()
        {
            if (is_null($this->connection)) {
                $this->connection = $this->createConnection();
            }

            return $this->connection;
        }

        public static function getInstance(Config $config)
        {
            if (static::$instance === null) {
                static::$instance = new static($config);
            }

            return static::$instance;
        }

        private function createConnection()
        {
            $databaseType = $this->config->get("database.default");
            $db = $this->config->get("database.$databaseType.database");
            $user = $this->config->get("database.$databaseType.user");
            $pass = $this->config->get("database.$databaseType.password");
            $host = $this->config->get("database.$databaseType.host");
            $port = $this->config->get("database.$databaseType.port");

            try {
                $dsn = $databaseType . ':host=' . $host . ';port=' . $port . ';dbname=' . $db;
                return new \PDO($dsn, $user, $pass);
            } catch (\PDOException $e) {
                throw new \Exception("Can't connect to database: " . $e->getMessage());
            }
        }

        public function insert(string $table, array $data)
        {
            $columns = array_keys($data);

            $binds = [];
            foreach ($data as $column => $value) {
                $binds[":$column"] = $value;
            }

            $sql = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES (" . implode(",", array_keys($binds)) . ")";

            return $this->query($sql, $binds);
        }

        public function query(string $sql, array $params = [])
        {
            try {
                $query = $this->connection->prepare($sql);

                if (!empty($params)) {
                    foreach ($params as $param => $val) {
                        $query->bindValue($param, $val);
                    }
                }

                $query->execute();
                return $query;
            } catch (\Exception $e) {
                exit($e->getMessage());
            }
        }
    }