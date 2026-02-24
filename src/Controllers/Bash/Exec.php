<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use helpers\BashRequest;

class Exec extends AbstractController
{
    public function getMethod(): void
    {
        $helper = new BashRequest();
        $helper->control();
    }

    public function support(string $method): bool
    {
        return $method === 'GET' || $method === 'POST';
    }
}
