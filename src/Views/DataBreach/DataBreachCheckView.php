<?php

namespace Views\DataBreach;

use Views\AbstractView;

class DataBreachCheckView extends AbstractView
{
    function templatePath(): string
    {
        return __DIR__ . '/data-breach-check.html';
    }

    function templateKeys(): array
    {
        return [];
    }

    protected function extraHeadContent(): string
    {
        return '<link rel="stylesheet" href="/assets/css/header.css?v=5" type="text/css">
        <link rel="stylesheet" href="/assets/css/data-breach-check.css?v=2" type="text/css">';
    }
}
