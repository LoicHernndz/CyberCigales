<?php
namespace Views\Game\FrequencyGame;

use Views\AbstractView;

class FrequencyGameView extends AbstractView
{
    private array $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    function templatePath(): string
    {
        return __DIR__ . '/frequency-game.html';
    }
    
    function templateKeys(): array
    {
        return [
            'USERNAME_KEY' => htmlspecialchars($this->data['username'] ?? 'Analyste')
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
        <title>Analyse Fréquentielle - CyberCigales</title>
        <link rel="stylesheet" href="/styles/main.css" type="text/css">
        <link rel="stylesheet" href="/styles/frequency-game.css" type="text/css">
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    </head>
    <body>';
    }
    
    function renderFooter(): void
    {
        echo '
        <script src="/js/frequency-game.js"></script>
    </body>
</html>';
    }
}
