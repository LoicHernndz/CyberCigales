<?php

namespace Views\User;
use Models\User\User;
use Views\AbstractView;

class SignupView extends AbstractView {

    private const FLASH_KEY = 'FLASH';
    private const TEMPLATE_HTML = __DIR__ . '/signup.html';

    public function templatePath() : string {
        return self::TEMPLATE_HTML; 
    }

    public function templateKeys() : array {
        return [self::FLASH_KEY => flash('signup')];
    }
}