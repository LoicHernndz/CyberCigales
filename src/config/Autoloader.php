<?php

/**
 * Autochargement des classes du projet (PSR-4)
 * 
 * Enregistre un autoloader qui convertit les namespaces en chemins de fichiers.
 */
namespace config;

class Autoloader
{
    /**
     * Enregistre l'autoloader SPL
     * 
     * Convertit les namespaces en chemins de fichiers et charge automatiquement
     * les classes depuis le répertoire src/.
     * 
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}

Autoloader::register();