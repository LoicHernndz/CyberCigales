<?php

namespace config;

use Attributes\Route;
use ReflectionClass;

class RouteRegistry
{
    /** @var array<string, string> name => path */
    private static array $routes = [];

    private static bool $built = false;

    public static function build(): void
    {
        if (self::$built) return;

        $controllerDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Controllers';
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($controllerDir)
        );

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') continue;

            $relativePath = str_replace(
                [realpath($controllerDir) . DIRECTORY_SEPARATOR, '.php', DIRECTORY_SEPARATOR],
                ['', '', '\\'],
                realpath($file->getPathname())
            );
            $className = 'Controllers\\' . $relativePath;

            if (!class_exists($className)) continue;

            $ref = new ReflectionClass($className);
            $attrs = $ref->getAttributes(Route::class);

            foreach ($attrs as $attr) {
                $route = $attr->newInstance();
                self::$routes[$route->name] = $route->path;
            }
        }

        self::$built = true;
    }

    public static function url(string $name): string
    {
        if (!self::$built) self::build();

        if (!isset(self::$routes[$name])) {
            throw new \RuntimeException("Route '{$name}' introuvable dans le registre.");
        }

        return self::$routes[$name];
    }

    /** @return array<string, string> */
    public static function all(): array
    {
        if (!self::$built) self::build();
        return self::$routes;
    }
}
