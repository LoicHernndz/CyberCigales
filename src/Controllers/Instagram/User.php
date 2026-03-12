<?php

namespace Controllers\Instagram;

use Controllers\AbstractController;
use Attributes\Route;

/**
 * Contrôleur des profils utilisateurs Instagram
 *
 * Gère l'affichage des profils Instagram en utilisant les paramètres
 * dynamiques du routeur pour identifier l'utilisateur et l'action.
 */
#[Route('/instagram/user', name: 'instagram_user')]
class User extends AbstractController
{
    /**
     * Affiche le profil ou le chat d'un utilisateur Instagram
     *
     * Récupère le nom d'utilisateur et l'action depuis les paramètres de route.
     * Redirige vers /instagram si aucun paramètre n'est fourni.
     *
     * @return void
     */
    public function getMethod(): void
    {
        $params = $_REQUEST['route_params'] ?? [];

        if (empty($params)) {
            redirect(url('instagram'));
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
