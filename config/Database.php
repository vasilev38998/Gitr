<?php

namespace App\Config;

class Database
{
    private static $instance = null;
    private $connection = null;

    private function __construct()
    {
        $config = require __DIR__ . '/database.php';

        try {
            $this->connection = new \mysqli(
                $config['host'],
                $config['user'],
                $config['password'],
                $config['database'],
                $config['port']
            );

            if ($this->connection->connect_error) {
                throw new \Exception('Database connection failed: ' . $this->connection->connect_error);
            }

            $this->connection->set_charset($config['charset']);
        } catch (\Exception $e) {
            die('Database Error: ' . $e->getMessage());
        }
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
        return $this->connection;
    }

    private function __clone() {}
    private function __wakeup() {}
}
