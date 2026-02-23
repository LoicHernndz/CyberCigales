<?php

namespace config;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Attributes\Route;

/**
 * Configuration des routes de l'application
 * 
 * Associe les URLs aux contrôleurs correspondants.
 * Format : 'chemin URL' => 'Namespace\Classe'
 */
class Routes
{
    /**
     * Table de routage URL vers contrôleur
     * 
     * @var array<string, string>
     */
    static public array $routes = [
        // NOTE : Les routes sont chargées dynamiquement via les Attributs dans initRoutes().
        // Les entrées ci-dessous sont maintenues temporairement le temps de migrer tous les contrôleurs.
        
        '/user/signup' => 'Controllers\User\Signup',
        '/user/reset-password' => 'Controllers\User\ResetPassword',
        '/user/new-password' => 'Controllers\User\CreateNewPassword',
        '/user/edit' => 'Controllers\User\EditProfil',
        '/user/delete' => 'Controllers\User\DeleteProfil',
        '/user/logout' => 'Controllers\User\Logout',
        '/user/profil' => 'Controllers\User\Profil',

        '/captcha' => 'Controllers\Captcha',

        '/cypher-rush' => 'Controllers\Game\CypherRush',
        '/game/frequency' => 'Controllers\Game\FrequencyGame',
        '/game/phishing' => 'Controllers\Game\PhishingGame',
        '/game/hamming' => 'Controllers\Game\Hamming',

        '/code/chiffrement-cesar' => 'Controllers\Code\Cesar\EncryptCesar',
        '/code/dechiffrement-cesar' => 'Controllers\Code\Cesar\DecryptCesar',
        '/code/chiffrement-vigenere' => 'Controllers\Code\Vigenere\EncryptVigenere',
        '/code/dechiffrement-vigenere' => 'Controllers\Code\Vigenere\DecryptVigenere',
        '/code/chiffrement-permutation' => 'Controllers\Code\Permutation\EncryptPermutation',
        '/code/dechiffrement-permutation' => 'Controllers\Code\Permutation\DecryptPermutation',
        '/code/outil-permutation' => 'Controllers\Code\Permutation\PermutationDecryptTool',

        '/instagram' => 'Controllers\Instagram\Instagram',

        '/macos' => 'Controllers\MacOSController',

        '/lecon' => 'Controllers\Lecon\AllLessonPage',
        '/LeconHistMdp' => 'Controllers\Lecon\LeconHistMdp',
        '/LeconCesar' => 'Controllers\Lecon\LeconCesar',
        '/LeconPermutation' => 'Controllers\Lecon\LeconPermutation',
        '/LeconVigenere' => 'Controllers\Lecon\LeconVigenere',
        '/lecon-rgpd' => 'Controllers\Lecon\RgpdPresentation',

        '/email' => 'Controllers\InterfaceMail\InterfaceMail',
        '/agenda' => 'Controllers\InterfaceAgenda\InterfaceAgenda',
        '/web' => 'Controllers\InterfaceWeb\InterfaceWeb',
        '/bash' => 'Controllers\Bash\Bash',
        '/bash/exec' => 'helpers\BashRequest',
        '/instagram/chat/response' => 'helpers\GenerateAnswer',
        '/minigames' => 'Controllers\Minigames\Minigames',
        '/outils' => 'Controllers\Code\AllCodePage\AllCodePage',

        '/data-breach/check' => 'Controllers\DataBreach\DataBreachCheck',
    ];

    /**
     * Table de routage Nom vers URL (pour la génération de liens)
     * 
     * @var array<string, string>
     */
    static public array $namedRoutes = [];

    /**
     * Initialise les routes en scannant les contrôleurs
     */
    public static function initRoutes(): void
    {
        $controllersDir = __DIR__ . '/../Controllers';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllersDir));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = self::getClassNameFromFile($file->getPathname());
                if ($className && class_exists($className)) {
                    $reflection = new ReflectionClass($className);
                    $attributes = $reflection->getAttributes(Route::class);
                    
                    foreach ($attributes as $attribute) {
                        $route = $attribute->newInstance();
                        self::$routes[$route->path] = $className;
                        self::$namedRoutes[$route->name] = $route->path;
                    }
                }
            }
        }
    }

    /**
     * Extrait le nom complet de la classe (avec namespace) d'un fichier PHP
     */
    private static function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        $namespace = null;
        $class = null;
        
        if (preg_match('/namespace\s+(.+?);/', $content, $matches)) {
            $namespace = $matches[1];
        }
        
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $class = $matches[1];
        }
        
        if ($namespace && $class) {
            return $namespace . '\\' . $class;
        }
        
        return null;
    }
}

// Initialisation des routes au chargement de la classe
Routes::initRoutes();
