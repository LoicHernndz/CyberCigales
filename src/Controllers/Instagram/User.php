<?php

namespace Controllers\Instagram;

use Controllers\AbstractController;

class User extends AbstractController
{
    function getMethod()
    {
        $params = $_REQUEST['route_params'] ?? [];
        
        if (empty($params)) {
            header('Location: /instagram');
            exit;
        }
        
        $username = $params[0];
        $action = $params[1] ?? null;
        
        if ($action === 'chat') {
            $controller = new UserChat();
            $controller->control();
        } else {
            $controller = new UserProfile();
            $controller->control();
        }
    }
}
