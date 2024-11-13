<?php

namespace App\Services;

abstract class BaseService
{
    private static array $instances = [];
    protected $model;

    public function __construct()
    {
        $this->initializeModel();
    }

    abstract protected function initializeModel();

    public static function getInstance(): static
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}