<?php

namespace Controllers\Instagram\Chat;

use Controllers\AbstractController;
use helpers\GenerateAnswer;

class Response extends AbstractController
{
    function getMethod()
    {
        $helper = new GenerateAnswer();
        $helper->control();
    }

    function postMethod()
    {
        $helper = new GenerateAnswer();
        $helper->control();
    }
}
