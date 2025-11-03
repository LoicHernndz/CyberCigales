<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\User;
use Views\User\EditProfil\EditProfilView;

class EditProfil extends AbstractController
{
    function getMethod()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect('/user/login');
            return;
        }

        // Afficher le formulaire d'édition
        $view = new EditProfilView();
        $view->render();
    }

    function postMethod()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect('/user/login');
            return;
        }

        // Valider et traiter le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $errors = [];

            // Récupérer les données du formulaire
            $userId = $_SESSION['user_id'];
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $pseudo = trim($_POST['pseudo'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Validation
            if (empty($prenom)) {
                $errors[] = 'Le prénom est requis.';
            }

            if (empty($nom)) {
                $errors[] = 'Le nom est requis.';
            }

            if (empty($pseudo)) {
                $errors[] = 'Le pseudo est requis.';
            }

            if (empty($email)) {
                $errors[] = 'L\'email est requis.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email n\'est pas valide.';
            }

            // Vérifier si le pseudo ou l'email est déjà utilisé par un autre utilisateur
            if (empty($errors)) {
                $userModel = new User();
                $existingUser = $userModel->findUserByEmailOrUsername($email, $pseudo);

                if ($existingUser && $existingUser->id != $userId) {
                    $errors[] = 'Ce pseudo ou cet email est déjà utilisé par un autre compte.';
                }
            }

            // Si des erreurs existent, les afficher
            if (!empty($errors)) {
                flash('edit_profil', implode('<br>', $errors), 'form-message form-message-red');
                redirect('/user/edit');
                return;
            }

            // Mettre à jour les informations
            $userModel = new User();
            $success = $userModel->updateUserInfo($userId, [
                'prenom' => $prenom,
                'nom' => $nom,
                'pseudo' => $pseudo,
                'email' => $email
            ]);

            if ($success) {
                // Mettre à jour la session
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_pseudo'] = $pseudo;
                $_SESSION['user_email'] = $email;

                flash('edit_profil', 'Vos informations ont été mises à jour avec succès !', 'form-message form-message-green');
                redirect('/user/profil');
            } else {
                flash('edit_profil', 'Une erreur est survenue lors de la mise à jour.', 'form-message form-message-red');
                redirect('/user/edit');
            }
        }
    }
}

