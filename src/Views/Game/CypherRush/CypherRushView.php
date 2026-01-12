<?php
namespace Views\Game\CypherRush;

use Views\AbstractView;

class CypherRushView extends AbstractView
{
    private array $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    function templatePath(): string
    {
        return __DIR__ . '/cypher-rush.html';
    }
    
    function templateKeys(): array
    {
        return [
            'USERNAME_KEY' => htmlspecialchars($this->data['username'] ?? $_SESSION['username'] ?? 'Analyste'),
            'SCORE_KEY' => $this->data['score'] ?? $_SESSION['cypher_score'] ?? 0
        ];
    }
    
    function renderHeader(): void
    {
        echo '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Cypher Rush - Mini-jeu de déchiffrement CyberCigales">
        <title>Cypher Rush - CyberCigales</title>
        <link rel="stylesheet" href="/styles/main.css" type="text/css">
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    </head>
    <body>';
    }
    
    function renderFooter(): void
    {
        echo '
        <!-- Script cypher-rush.js supprimé car non utilisé -->
    </body>
</html>';
    }
}

