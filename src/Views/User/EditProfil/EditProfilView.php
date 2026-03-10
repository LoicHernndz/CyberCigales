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
        $keys['PRENOM_KEY'] = htmlspecialchars($_SESSION['user_prenom'] ?? '', ENT_QUOTES, 'UTF-8');
        $keys['NOM_KEY'] = htmlspecialchars($_SESSION['user_nom'] ?? '', ENT_QUOTES, 'UTF-8');
        $keys['PSEUDO_KEY'] = htmlspecialchars($_SESSION['user_pseudo'] ?? '', ENT_QUOTES, 'UTF-8');
        $keys['EMAIL_KEY'] = htmlspecialchars($_SESSION['user_email'] ?? '', ENT_QUOTES, 'UTF-8');
        $keys['FLASH_MESSAGE'] = flash('edit_profil');
        // OWASP A01 : token CSRF injecté dans les formulaires (édition + suppression)
        $keys['CSRF_FIELD'] = csrf_field();

        return $keys;
    }
}

