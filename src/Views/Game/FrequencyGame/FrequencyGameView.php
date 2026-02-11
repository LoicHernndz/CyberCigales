<?php
/**
 * Vue du mini-jeu Analyse Frequentielle
 * 
 * Utilise le meme CSS que phishing-game pour la coherence visuelle.
 */
namespace Views\Game\FrequencyGame;

use Views\AbstractView;

class FrequencyGameView extends AbstractView
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function templatePath(): string
    {
        return __DIR__ . '/frequency-game.html';
    }

    public function templateKeys(): array
    {
        return [
            'USERNAME_KEY' => htmlspecialchars($this->data['username'] ?? 'Analyste')
        ];
    }

    public function renderHeader(): void
    {
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Frequentielle - CyberCigales</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/styles/game-base.css">
</head>
<body>';
    }

    public function renderFooter(): void
    {
        echo '
    </body>
</html>';
    }
}
