<?php

namespace Views\Lecon\LeconPermutation;

use Views\AbstractView;

class LeconPermutationView extends AbstractView {

    private const TEMPLATE_HTML = __DIR__ . '/LeconPermutation.html';
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
