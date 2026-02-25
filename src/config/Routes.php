<?php

namespace config;

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
        '/' => 'Controllers\Homepage',
        '/plan' => 'Controllers\SitePlan',
        '/mentions' => 'Controllers\Mentions',
        '/dashboard' => 'Controllers\Dashboard',

        '/user/login' => 'Controllers\User\Login',
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
        '/instagram/unlock-post' => 'Controllers\Instagram\UnlockPost',
        '/minigames' => 'Controllers\Minigames\Minigames',
        '/outils' => 'Controllers\Code\AllCodePage\AllCodePage',

        '/data-breach/check' => 'Controllers\DataBreach\DataBreachCheck',
    ];

}