<?php

namespace config;

class Routes
{
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

        '/cypher-rush' => 'Controllers\Game\CypherRush',

        '/qcm/rgpd/resultat' => 'Controllers\Qcm\QcmResultat',
        '/qcm/rgpd/feedback' => 'Controllers\Qcm\QcmFeedback',
        '/qcm/rgpd' => 'Controllers\Qcm\QcmRgpd',
        '/qcm/rgpd/presentation' => 'Controllers\Qcm\RgpdPresentation',

        '/code/chiffrement-cesar' => 'Controllers\Code\Cesar\EncryptCesar',
        '/code/dechiffrement-cesar' => 'Controllers\Code\Cesar\DecryptCesar',
        '/code/chiffrement-vigenere' => 'Controllers\Code\Vigenere\EncryptVigenere',
        '/code/dechiffrement-vigenere' => 'Controllers\Code\Vigenere\DecryptVigenere',
        '/code/chiffrement-permutation' => 'Controllers\Code\Permutation\EncryptPermutation',
        '/code/dechiffrement-permutation' => 'Controllers\Code\Permutation\DecryptPermutation',

        '/instagram' => 'Controllers\Instagram\Instagram',
        '/instagram/melina' => 'Controllers\Instagram\MelinaProfile',
        '/instagram/melina/chat' => 'Controllers\Instagram\MelinaChat',
        '/LeconCesar' => 'Controllers\Lecon\LeconCesar',
        '/LeconPermutation' => 'Controllers\Lecon\LeconPermutation',
        '/LeconVigenere' => 'Controllers\Lecon\LeconVigenere',
        '/instagram/melina/chat' => 'Controllers\Instagram\MelinaChat',
        '/LeconHistMdp' => 'Controllers\Lecon\LeconHistMdp',
        '/macos' => 'Controllers\MacOSController',

        '/bash' => 'Controllers\Bash\Bash',
        '/bash/exec' => 'helpers\BashRequest',
        ];

}