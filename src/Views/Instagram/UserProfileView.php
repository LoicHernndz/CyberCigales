<?php
namespace Views\Instagram;

/**
 * Vue pour le profil Instagram d'un utilisateur générique
 * 
 * Cette classe gère l'affichage du template HTML du profil utilisateur.
 * Elle hérite de BaseInstagramView pour les fonctionnalités communes.
 */
class UserProfileView extends BaseInstagramView
{

    private const TEMPLATE_PATH = __DIR__ . '/user-profile.html';

    public function templatePath() : string {
        return self::TEMPLATE_PATH;
    }
}

