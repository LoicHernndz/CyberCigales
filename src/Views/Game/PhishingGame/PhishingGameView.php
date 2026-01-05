<?php

namespace Views\Game\PhishingGame;

use Views\AbstractView;

class PhishingGameView extends AbstractView
{
    public function templatePath(): string
    {
        return __DIR__ . '/phishing-game.html';
    }

    public function templateKeys(): array
    {
        return [];
    }
}
