<?php

namespace Views\User\CreateNewPassword;

use Views\AbstractView;

class CreateNewPasswordView extends AbstractView
{
    private const TEMPLATE_HTML = __DIR__ . ‘/create-new-password.html’;

    private array $additionalKeys = [];

    public function addTemplateKey(string $key, $value): void
    {
        $this->additionalKeys[$key] = $value;
    }

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        return array_merge([
            ‘FLASH’ => flash(‘new-password’),
        ], $this->additionalKeys);
    }
}
