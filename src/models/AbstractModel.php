<?php

namespace App\Models;

use App\database;

abstract class AbstractModel
{
    protected \PDO $db;
    protected string $table;

    public function __construct()
    {
        // print that you began
        // echo "Model: " . get_class($this) . " has been created. <br />";


        $this->db = Database::getInstance()->getConnection();
        if (!$this->db) {
            throw new \Exception("Database connection failed.");
        }
    }

    abstract public function find($id);
    abstract public function findAll();
    abstract public function create(array $data);
    abstract public function update($id, array $data);
    abstract public function delete($id);
}