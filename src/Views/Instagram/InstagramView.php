<?php
namespace Views\Instagram;

/**
 * Vue pour la page d'accueil Instagram
 * 
 * Cette classe gère l'affichage du template HTML de l'accueil Instagram.
 * Elle hérite de BaseInstagramView pour les fonctionnalités communes.
 * 
 * Fonctionnalités :
 * - Gestion des clés de template ({{STORIES}}, {{POSTS}})
 * - Rendu sans header/footer CyberCigales (page standalone)
 */
class InstagramView extends BaseInstagramView
{
    
    // Chemin du fichier HTML template
    private const TEMPLATE_PATH = __DIR__ . '/instagram.html';
    
    /**
     * Méthode qui retourne le chemin du fichier HTML à utiliser pour le rendu
     * 
     * @return string Chemin vers le template HTML
     */
    public function templatePath() : string {
        return self::TEMPLATE_PATH;
    }
}
