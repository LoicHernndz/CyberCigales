<?php
namespace Views\Game\Hamming;

use Views\AbstractView;

/**
 * Vue pour le mini-jeu du carré de Hamming
 * 
 * Affiche un carré 3x3 interactif où l'utilisateur doit identifier
 * le bit erroné. Page autonome sans header/footer pour intégration.
 */
class HammingView extends AbstractView
{
    /**
     * Données du carré et du résultat
     * 
     * @var array Contient :
     *            - 'square' : carré 3x3 à afficher
     *            - 'success' : résultat (1 = correct, 0 = incorrect, null = pas encore de réponse)
     *            - 'message' : message de feedback
     *            - 'streak' : nombre de victoires consécutives
     *            - 'level' : niveau actuel
     *            - 'target' : objectif de victoires pour passer au niveau suivant
     */
    private array $data;
    
    /**
     * Constructeur
     * 
     * @param array $data Données du carré et du résultat
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    /**
     * Retourne le chemin du template HTML
     * 
     * @return string Chemin vers le fichier hamming.html
     */
    function templatePath(): string
    {
        return __DIR__ . '/hamming.html';
    }
    
    /**
     * Retourne les clés de template à remplacer dans le HTML
     * 
     * Convertit le carré 3x3 en HTML et prépare le message de résultat
     * 
     * @return array Tableau associatif des clés de remplacement
     */
    function templateKeys(): array
    {
        $square = $this->data['square'] ?? [[0,0,0],[0,0,0],[0,0,0]];
        // JSON directement - s'assurer que c'est bien une string
        $squareJson = (string)json_encode($square);
        
        return [
            'SQUARE_JSON' => $squareJson
        ];
    }
    
    /**
     * Override renderBody pour éviter le nettoyage des accolades
     */
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());
        
        foreach($this->templateKeys() as $key => $value){
            // Convertir en string pour éviter les problèmes de type
            $value = (string)$value;
            // Remplacer toutes les occurrences de la clé
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        
        // Ne PAS nettoyer les accolades pour SQUARE_JSON
        // Laisser les autres placeholders être nettoyés si nécessaire
        
        echo $template;
    }
    
    /**
     * Affiche le header HTML de la page (sans header du site)
     * 
     * @return void
     */
    function renderHeader(): void
    {
        echo '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carré de Hamming - CyberCigales</title>
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
        <link rel="stylesheet" href="/styles/hamming.css?v=1" type="text/css">
    </head>
    <body>';
    }
    
    /**
     * Affiche le footer HTML
     * 
     * @return void
     */
    function renderFooter(): void
    {
        echo '
    </body>
</html>';
    }
}


