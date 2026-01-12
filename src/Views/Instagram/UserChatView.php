<?php
namespace Views\Instagram;

/**
 * Vue pour le chat Instagram avec un utilisateur générique
 * 
 * Cette classe gère l'affichage du template HTML du chat.
 * Elle hérite de BaseInstagramView pour les fonctionnalités communes.
 */
class UserChatView extends BaseInstagramView
{

    private const TEMPLATE_PATH = __DIR__ . '/user-chat.html';

    public function templatePath() : string {
        return self::TEMPLATE_PATH;
    }
}

