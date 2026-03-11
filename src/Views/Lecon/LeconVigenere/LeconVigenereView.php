<?php

namespace Views\Lecon\LeconVigenere;

use Views\AbstractView;

class LeconVigenereView extends AbstractView {

    private const TEMPLATE_HTML = __DIR__ . '/LeconVigenere.html';
    private array $additionalKeys = [];

    public function addTemplateKey(string $key, $value): void
    {
        $this->additionalKeys[$key] = $value;
    }

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        return $this->additionalKeys;
    }
}
