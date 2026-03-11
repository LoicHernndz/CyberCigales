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

    public function templateKeys(): array
    {
        return $this->additionalKeys;
    }
}
