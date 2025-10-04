<?php

namespace Views\User;
use Views\AbstractView;

class LoginView extends AbstractView {

    private const FLASH_KEY = 'FLASH';
    private String $message;
    private String $class;

    public function __construct($message = "", $class = 'form-message form-message-red') {
        $this->message = $message;
        $this->class = $class;
    }
    private const TEMPLATE_HTML = __DIR__ . '/login.html';

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        return [self::FLASH_KEY => flash('login', $this->message, $this->class)];
    }
}