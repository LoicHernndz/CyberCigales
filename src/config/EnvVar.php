<?php 
namespace config;
class EnvVar
{
    public static function getEnvVars(): array
    {
        $envFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
        if (!file_exists($envFile)) {
            error_log($envFile . ' file not found');
            return [];
        }

        return parse_ini_file($envFile);
    }

    public static function get(string $key): ?string
    {
        $envVars = self::getEnvVars();
        return $envVars[$key] ?? $_ENV[$key] ?? null;
    }
}