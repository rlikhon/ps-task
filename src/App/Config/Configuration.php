<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Config;

/**
 * Class Configuration.
 */
class Configuration
{
    /**
     * Configuration instance.
     *
     * @var Configuration|null
     */
    private static $instances = null;

    /** List of Configuration values.
     * @var array
     */
    private static $values = [];

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    /**
     * Get Configuration instance.
     */
    public static function getInstance(): Configuration
    {
        if (!isset(self::$instances)) {
            self::$instances = new static();
        }

        return self::$instances;
    }

    /**
     * Initiate Configuration values from array.
     *
     * @param $values
     */
    public static function load($values): void
    {
        self::$values = $values;
    }

    /**
     * Get Configuration value by key.
     *
     * @param $name
     */
    public static function get($name, string $default = null): ?string
    {
        if (isset(self::$values[$name])) {
            return self::$values[$name];
        } else {
            return $default;
        }
    }
}
