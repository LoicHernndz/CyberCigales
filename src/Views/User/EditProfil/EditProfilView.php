<?php

namespace Views\User\EditProfil;

use Views\AbstractView;

class EditProfilView extends AbstractView
{
    public function templatePath(): string
    {
        return __DIR__ . '/edit-profil.html';
    }

    public function templateKeys(): array
    {
        $keys = [];
        
        // Récupérer les informations de l'utilisateur depuis la session
        $keys['PRENOM_KEY'] = $_SESSION['user_prenom'] ?? '';
        $keys['NOM_KEY'] = $_SESSION['user_nom'] ?? '';
        $keys['PSEUDO_KEY'] = $_SESSION['user_pseudo'] ?? '';
        $keys['EMAIL_KEY'] = $_SESSION['user_email'] ?? '';
        $keys['FLASH_MESSAGE'] = flash('edit_profil');
        $keys['CSRF_FIELD'] = csrf_field();

        return $keys;
    }
}

