<?php

namespace Views\Homepage;

use Views\AbstractView;

class HomepageView extends AbstractView
{
    private const TEMPLATE_LOGGED = __DIR__ . '/homepage.html';
    private const TEMPLATE_GUEST = __DIR__ . '/homepage-guest.html';

    private array $additionalKeys = [];

    public function addTemplateKey(string $key, $value): void
    {
        $this->additionalKeys[$key] = $value;
    }

    public function templatePath(): string
    {
        if (isset($_SESSION['user_id'])) {
            return self::TEMPLATE_LOGGED;
        } else {
            return self::TEMPLATE_GUEST;
        }
    }

    public function setEscapeGameData(bool $unlocked, string $escapeUrl, int $lessonsCompleted): void
    {
        if ($unlocked) {
            $this->additionalKeys['ESCAPE_CARD'] = '<a href="' . htmlspecialchars($escapeUrl) . '" class="concept-card">
                    <div class="concept-icon"><span class="material-icons">sports_esports</span></div>
                    <h3>Notre Escape Game</h3>
                    <p>Mettez en pratique toutes les connaissances que vous avez acquises au travers de cet escape game interactif et ludique.</p>
                    <span class="btn-card-action"><span>Lancer l\'escape game</span><span class="material-icons">play_arrow</span></span>
                </a>';
        } else {
            $this->additionalKeys['ESCAPE_CARD'] = '<div class="concept-card escape-locked">
                    <div class="escape-lock-overlay"><span class="material-icons">lock</span></div>
                    <div class="concept-icon"><span class="material-icons">sports_esports</span></div>
                    <h3>Notre Escape Game</h3>
                    <p>Terminez les 3 leçons obligatoires (César, Vigenère, Permutation) pour débloquer l\'escape game.</p>
                    <span class="btn-card-action btn-card-disabled"><span>Verrouillé — ' . $lessonsCompleted . '/3 leçons</span><span class="material-icons">lock</span></span>
                </div>';
        }
    }

    public function templateKeys(): array
    {
        return $this->additionalKeys;
    }
}
