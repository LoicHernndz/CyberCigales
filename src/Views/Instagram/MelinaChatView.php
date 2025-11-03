<?php
namespace Views\Instagram;

/**
 * Vue pour le chat avec Melina
 * 
 * Cette classe gère l'affichage du template HTML du chat avec Melina.
 * Elle hérite de BaseInstagramView pour les fonctionnalités communes.
 */
class MelinaChatView extends BaseInstagramView
{
    public function templatePath(): string
    {
        return __DIR__ . '/melina-chat.html';
    }
}
