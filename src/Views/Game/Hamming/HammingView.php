<?php
namespace Views\Game\Hamming;

use Views\AbstractView;

/**
 * Vue pour le mini-jeu Hamming intégré à l'interface MacOS.
 * 
 * Affiche un carré 3x3 interactif où l'utilisateur doit identifier
 * le bit erroné. Page autonome optimisée pour iframe (style glassmorphism).
 * 
 * Template keys :
 * - SQUARE_JSON : Carré 3x3 encodé en JSON (array de 3 arrays de 3 int)
 * - GAME_DATA : Données de progression JSON {streak: int, target: int}
 */
class HammingView extends AbstractView
{
    /** @var array Données du jeu (square, streak, target) */
    private array $data;
    
    /**
     * @param array $data Données du jeu :
     *                    - 'square' : array 3x3 du carré de Hamming
     *                    - 'streak' : int série de victoires actuelle
     *                    - 'target' : int objectif de victoires (défaut 5)
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    /**
     * @return string Chemin vers hamming.html
     */
    function templatePath(): string
    {
        return __DIR__ . '/hamming.html';
    }
    
    /**
     * Génère les clés de template pour le HTML.
     * 
     * @return array [
     *     'SQUARE_JSON' => string JSON du carré 3x3,
     *     'GAME_DATA' => string JSON {streak, target}
     * ]
     */
    function templateKeys(): array
    {
        $square = $this->data['square'] ?? [[0,0,0],[0,0,0],[0,0,0]];
        $streak = $this->data['streak'] ?? 0;
        $target = $this->data['target'] ?? 5;
        
        return [
            'SQUARE_JSON' => json_encode($square),
            'GAME_DATA' => json_encode(['streak' => $streak, 'target' => $target])
        ];
    }
    
    /**
     * Charge et affiche le template avec remplacement des clés.
     */
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());
        
        foreach($this->templateKeys() as $key => $value){
            $template = str_replace("{{{" . $key . "}}}", (string)$value, $template);
        }
        
        echo $template;
    }
    
    /**
     * Affiche le header HTML minimal (optimisé pour iframe MacOS).
     */
    function renderHeader(): void
    {
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hamming Rush</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="/images/icons/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/game-base.css">
</head>
<body>';
    }
    
    /**
     * Affiche le footer HTML.
     */
    function renderFooter(): void
    {
        echo '</body>
</html>';
    }
}
