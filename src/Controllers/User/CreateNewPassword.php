<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\ResetPasswords;
use Models\User\User;
use PHPMailer\src\PHPMailer;
use Views\User\CreateNewPassword\CreateNewPasswordView;

class CreateNewPassword extends AbstractController
{
    // Propriétés privées pour les modèles et PHPMailer
    private $resetModel;
    private $userModel;
    private $mail;

    // Méthode principale exécutée lorsque la route correspondante est appelée
    function getMethod(){
        // Vérifie si les paramètres "selector" et "validator" sont absents de l’URL
        if(empty($_GET['selector']) || empty($_GET['validator'])) {
            // Si un paramètre est manquant, affiche un message d’erreur
            echo "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.";
        } else {
            // Récupère les valeurs envoyées dans l’URL
            $selector = $_GET['selector'];
            $validator = $_GET['validator'];

            // Vérifie que les deux valeurs contiennent uniquement des caractères hexadécimaux
            if(ctype_xdigit($selector) && ctype_xdigit($validator)) {
                // Si les paramètres sont valides, on charge la vue du formulaire "Créer un nouveau mot de passe"
                $view = new CreateNewPasswordView();
                // Affiche la vue correspondante
                $view->render();
            } else {
                // Si les paramètres contiennent des caractères invalides, on affiche un message d’erreur
                echo "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.";
            }
        }
    }

    // Traite le formulaire de création d’un nouveau mot de passe
    public function postMethod()
    {
        // Modèle pour la table des réinitialisations de mots de passe
        $this->resetModel = new ResetPasswords;
        // Modèle pour la table des utilisateurs
        $this->userModel = new User;

        // Charger les variables d'environnement depuis le fichier .env
        $envFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
        $envVars = [];

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $envVars[trim($key)] = trim($value, '"\'');
                }
            }
        }

        // Configuration de PHPMailer avec variables d'environnement
        $this->mail = new PHPMailer();
        $this->mail->SMTPDebug = 0; // Pas de logs de débogage affichés
        $this->mail->isSMTP(); // Utilisation du protocole SMTP
        $this->mail->Host = $envVars['SMTP_HOST']; // Serveur SMTP
        $this->mail->SMTPAuth = true; // Activation de l'authentification SMTP
        $this->mail->Username = $envVars['SMTP_USER']; // Adresse d'envoi
        $this->mail->Password = $envVars['SMTP_PASS']; // Mot de passe ou clé d'application
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Chiffrement TLS
        $this->mail->Port = $envVars['SMTP_PORT']; // Port TLS standard

        // Nettoyage et récupération des données envoyées en POST
        $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $data = [
            'selector' => trim($_POST['selector']),
            'validator' => trim($_POST['validator']),
            'pwd' => trim($_POST['pwd']),
            'pwd-repeat' => trim($_POST['pwd-repeat'])
        ];

        // URL de redirection en cas d’erreur
        $url = 'https://benahmed.alwaysdata.net/user/new-password?selector=' . $data['selector'] .
            '&validator=' . $data['validator'];

        // Vérifications de base du formulaire
        if(empty($_POST['pwd']) || empty($_POST['pwd-repeat'])){
            // Si un champ est vide
            flash("new-password", "SVP remplissez tous les champs");
            redirect($url);
        } else if($data['pwd'] != $data['pwd-repeat']){
            // Si les mots de passe ne correspondent pas
            flash("new-password", "Les mots de passe ne correspondent pas");
            redirect($url);
        } else if(strlen($data['pwd']) < 6){
            // Si le mot de passe est trop court
            flash("new-password", "Le mot de passe doit contenir au moins 6 caractères");
            redirect($url);
        }

        // Vérifie si la demande de réinitialisation est toujours valide (non expirée)
        $currentDate = date("U");
        if(!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)){
            flash("new-password", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        // Vérifie la validité du token reçu
        $tokenBin = hex2bin($data['validator']); // Convertit le token hexadécimal en binaire
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken); // Compare avec le token haché stocké
        if(!$tokenCheck){
            // Si le token est invalide
            flash("new-password", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        // Récupère l’adresse e-mail associée au token
        $tokenEmail = $row->pwdResetEmail;

        // Vérifie que l’utilisateur existe bien
        if(!$this->userModel->findUserByEmailOrUsername($tokenEmail, $tokenEmail)){
            flash("new-password", "Il n'y a pas d'utilisateur avec cet email.");
            redirect($url);
        }

        // Hache le nouveau mot de passe avant de l’enregistrer
        $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);

        // Met à jour le mot de passe de l’utilisateur dans la base de données
        if(!$this->userModel->resetPassword($newPwdHash, $tokenEmail)){
            flash("new-password", "Il y a eu une erreur.");
            redirect($url);
        }

        // Supprime le token utilisé pour éviter une réutilisation
        if(!$this->resetModel->deleteEmail($tokenEmail)){
            flash("new-password", "Il y a eu une erreur.");
            redirect($url);
        }

        // Si tout s’est bien passé, affiche un message de succès et redirige vers la page de connexion
        flash("login", "Votre mot de passe a été mis à jour ! Vous pouvez vous connecter avec votre nouveau mot de passe.", 'form-message form-message-green');
        redirect("/user/login");
    }
}
