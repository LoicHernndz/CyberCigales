<?php
namespace Views\Instagram;

/**
 * Vue pour le profil Instagram de Melina
 * 
 * Cette classe gère l'affichage du template HTML du profil de Melina.
 * Elle hérite de BaseInstagramView pour les fonctionnalités communes.
 */
class MelinaProfileView extends BaseInstagramView
{

    private const TEMPLATE_PATH = __DIR__ . '/melina-profile.html';

    public function templatePath() : string {
        return self::TEMPLATE_PATH;
    }
}
