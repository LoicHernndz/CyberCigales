<?php

namespace Views\Lecon\LeconCesar;

use Views\AbstractView;

class LeconCesarView extends AbstractView {

    private const TEMPLATE_HTML = __DIR__ . '/LeconCesar.html';
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
