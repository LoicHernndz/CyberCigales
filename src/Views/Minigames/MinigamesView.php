<?php

namespace Views\Minigames;

use Views\AbstractView;

class MinigamesView extends AbstractView {

    public function templatePath(): string {
        return __DIR__ . '/minigames.html';
    }

    public function templateKeys(): array {
        return [];
    }
}
