<?php
namespace config;

/**
 * Gestionnaire des variables d'environnement
 * 
 * Charge et fournit l'accès aux variables définies dans le fichier .env
 */
class EnvVar
{
    /**
     * Charge les variables d'environnement depuis le fichier .env
     * 
     * @return array Tableau associatif des variables d'environnement
     */
    public static function getEnvVars(): array
    {
        $envFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
        if (!file_exists($envFile)) {
            error_log($envFile . ' file not found');
            return [];
        }

        return parse_ini_file($envFile);
    }

    /**
     * Récupère une variable d'environnement par sa clé
     * 
     * @param string $key Nom de la variable (ex: 'DB_HOST', 'DB_NAME')
     * @return string|null La valeur de la variable ou null si non trouvée
     */
    public static function get(string $key): ?string
    {
        $envVars = self::getEnvVars();
        return $envVars[$key] ?? $_ENV[$key] ?? null;
    }
}