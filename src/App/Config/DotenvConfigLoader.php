<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class DotenvConfigLoader.
 */
class DotenvConfigLoader extends FileLoader
{
    /**
     * Parse Dot env file.
     *
     * @param mixed $resource
     *
     * @return array|mixed
     */
    public function load($resource, string $type = null)
    {
        $dotenv = new Dotenv();
        $configValues = $dotenv->parse(file_get_contents($resource));

        return $configValues;
    }

    /**
     * Check if file type is supported.
     *
     * @param mixed $resource
     *
     * @return bool
     */
    public function supports($resource, string $type = null)
    {
        return is_string($resource) && 'env' === pathinfo(
                $resource,
                PATHINFO_EXTENSION
            );
    }
}
