<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\User;
use Views\User\EditProfil\EditProfilView;
use Attributes\Route;

/**
 * Contrôleur d'édition du profil utilisateur
 *
 * Gère l'affichage du formulaire d'édition (GET) et le traitement
 * des modifications de profil (POST) avec validation des données.
 */
#[Route('/user/edit', name: 'user_edit')]
class Edit extends AbstractController
{
    /**
     * Affiche le formulaire d'édition du profil
     *
     * Redirige vers la connexion si l'utilisateur n'est pas authentifié.
     *
     * @return void
     */
    public function getMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect(url('user_login'));
            return;
        }
        $view = new EditProfilView();
        $view->render();
    }

    /**
     * Traite les modifications du profil utilisateur
     *
     * Valide les champs (prénom, nom, pseudo, email), vérifie
     * l'unicité du pseudo/email, et met à jour les données en BDD et session.
     *
     * @return void
     */
    public function postMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            flash('edit_profil', 'Vous devez être connecté pour modifier votre profil.', 'form-message form-message-red');
            redirect(url('user_login'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $errors = [];
            $userId = $_SESSION['user_id'];
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $pseudo = trim($_POST['pseudo'] ?? '');
            $email = trim($_POST['email'] ?? '');

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

            if (empty($errors)) {
                $userModel = new User();
                $existingUser = $userModel->findUserByEmailOrUsername($email, $pseudo);
                if ($existingUser && $existingUser->id != $userId) {
                    $errors[] = 'Ce pseudo ou cet email est déjà utilisé par un autre compte.';
                }
            }

            if (!empty($errors)) {
                flash('edit_profil', implode(' | ', $errors), 'form-message form-message-red');
                redirect(url('user_edit'));
                return;
            }

            $userModel = new User();
            $success = $userModel->updateUserInfo($userId, [
                'prenom' => $prenom,
                'nom' => $nom,
                'pseudo' => $pseudo,
                'email' => $email
            ]);

            if ($success) {
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_pseudo'] = $pseudo;
                $_SESSION['user_email'] = $email;
                flash('edit_profil', 'Vos informations ont été mises à jour avec succès !', 'form-message form-message-green');
                redirect(url('user_profil'));
            } else {
                flash('edit_profil', 'Une erreur est survenue lors de la mise à jour.', 'form-message form-message-red');
                redirect(url('user_edit'));
            }
        }
    }
}
