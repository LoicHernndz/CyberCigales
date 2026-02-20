<?php

// Répertoire racine du projet
$projectRoot = dirname(__DIR__);

// Autoload : namespace Tests\ => tests/, tout autre classe => src/
spl_autoload_register(function ($class) use ($projectRoot) {
    if (strncmp($class, 'Tests\\', 6) === 0) {
        // Tests\Mocks\MockDatabase => tests/Mocks/MockDatabase.php
        $relative = substr($class, 6); // retire "Tests\"
        $file = $projectRoot . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    } else {
        // Toutes les autres classes proviennent de src/
        $file = $projectRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    }
    if (file_exists($file)) {
        require_once $file;
    }
});

// Stub pour les fonctions globales utilisées par les controllers
if (!function_exists('flash')) {
    function flash($name = '', $message = '', $class = ''): string
    {
        return '';
    }
}
if (!function_exists('redirect')) {
    function redirect($location): void
    { /* no-op en test */
    }
}
