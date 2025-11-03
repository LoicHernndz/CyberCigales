<?php
namespace Views\Instagram;

use Views\AbstractView;

/**
 * Vue de base pour toutes les pages Instagram
 * 
 * Cette classe abstraite fournit les fonctionnalités communes
 * à toutes les vues Instagram (header, navigation, etc.)
 */
abstract class BaseInstagramView extends AbstractView
{
    // Tableau des clés de template additionnelles
    protected array $additionalKeys = [];
    
    /**
     * Méthode pour ajouter une clé de template
     * 
     * @param string $key La clé du placeholder (ex: 'STORIES')
     * @param mixed $value La valeur à injecter (HTML généré)
     */
    public function addTemplateKey(string $key, $value): void {
        $this->additionalKeys[$key] = $value;
    }
    
    /**
     * Méthode qui fournit les variables à injecter dans le template HTML
     * 
     * @return array Tableau des clés et valeurs pour le template
     */
    public function templateKeys(): array {
        return $this->additionalKeys;
    }
    
    /**
     * Override de la méthode render pour ne pas utiliser le header/footer standard
     * 
     * Cette méthode évite d'afficher le header/footer CyberCigales
     * et affiche seulement le contenu Instagram (page standalone)
     */
    public function render(): void {
        $this->renderBody();
    }
}
