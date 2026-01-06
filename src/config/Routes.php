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
        '/game/hamming' => 'Controllers\Game\Hamming',

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
        '/LeconCesar' => 'Controllers\LeconCode\LeconCesar',
        '/LeconPermutation' => 'Controllers\LeconCode\LeconPermutation',
        '/LeconVigenere' => 'Controllers\LeconCode\LeconVigenere',
        '/instagram/melina/chat' => 'Controllers\Instagram\MelinaChat',

        '/macos' => 'Controllers\MacOSController',
        ];

}