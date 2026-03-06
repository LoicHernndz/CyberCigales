<?php

namespace Controllers\User;

use config\EnvVar;
use Controllers\AbstractController;
use Models\User\ResetPasswords;
use Models\User\User;
use PHPMailer\src\PHPMailer;
use Views\User\ResetPassword\ResetPasswordView;

/**
 * Contrôleur de réinitialisation de mot de passe
 * 
 * Gère la demande de réinitialisation de mot de passe par email.
 * Génère un token sécurisé et envoie un email avec lien de réinitialisation.
 */
class ResetPassword extends AbstractController
{
    private ResetPasswords $resetModel;
    private User $userModel;
    private PHPMailer $mail;

    /**
     * Affiche le formulaire de demande de réinitialisation
     * 
     * @return void
     */
    public function getMethod()
    {
        // Création d’une instance de la vue "ResetPasswordView"
        $view = new ResetPasswordView();
        // Affichage du contenu de la page (formulaire pour demander un lien de réinitialisation)
        $view->render();
    }

    /**
     * Traite la demande de réinitialisation de mot de passe
     * 
     * Génère un token unique, l'enregistre en base de données
     * et envoie un email avec le lien de réinitialisation.
     * 
     * @return void
     */
    public function postMethod()
    {
        // OWASP A01 - Broken Access Control : vérification du token CSRF
        $this->csrfVerify();

        // OWASP A07 - Rate Limiting : protection contre le spam de demandes de réinitialisation
        // On limite à 3 demandes par fenêtre de 15 minutes
        // Cela empêche un attaquant de bombarder une adresse email de liens de réinitialisation
        if (!\helpers\RateLimiter::check('reset_password', 3, 900)) {
            flash("reset", "Trop de demandes. Veuillez patienter avant de réessayer.");
            (new ResetPasswordView())->render();
            return;
        }
        \helpers\RateLimiter::record('reset_password');

        $this->resetModel = new ResetPasswords;
        $this->userModel = new User;

        // Configuration PHPMailer avec variables d'environnement
        $this->mail = new PHPMailer();
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = EnvVar::get('SMTP_HOST');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = EnvVar::get('SMTP_USER');
        $this->mail->Password = EnvVar::get('SMTP_PASS');
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = EnvVar::get('SMTP_PORT') ?? 587;
        $this->mail->CharSet = 'UTF-8';

        $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $usersEmail = trim($_POST['usersEmail']);

        if (empty($usersEmail)) {
            flash("reset", "Veuillez entrer un email");
            (new ResetPasswordView())->render();
            exit();
        }

        if (!filter_var($usersEmail, FILTER_VALIDATE_EMAIL)) {
            flash("reset", "Email invalide");
            (new ResetPasswordView())->render();
            exit();
        }

        // Vérifie si l'email existe (sans divulguer l'information)
        $userExists = $this->userModel->findUserByEmailOrUsername($usersEmail, $usersEmail);
        if (!$userExists) {
            // Affiche toujours le même message pour éviter la divulgation d'informations
            flash("reset", "Si cette adresse email est associée à un compte, vous recevrez un email de réinitialisation.", 'form-message form-message-green');
            (new ResetPasswordView())->render();
            exit();
        }

        // Création du token de réinitialisation
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        $url = "https://benahmed.alwaysdata.net/user/new-password?selector=" . $selector . "&validator=" . bin2hex($token);
        $expires = strval((int)(date("U")) + 1800);

        // OWASP A09 - Logging : on log les erreurs internes au lieu de les afficher
        // die() exposait des détails techniques à l'utilisateur (nom des fonctions internes)
        if (!$this->resetModel->deleteEmail($usersEmail)) {
            error_log('[ERROR] Échec deleteEmail pour la réinitialisation');
            flash("reset", "Une erreur est survenue. Veuillez réessayer plus tard.");
            (new ResetPasswordView())->render();
            return;
        }

        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        if (!$this->resetModel->insertToken($usersEmail, $selector, $hashedToken, $expires)) {
            error_log('[ERROR] Échec insertToken pour la réinitialisation');
            flash("reset", "Une erreur est survenue. Veuillez réessayer plus tard.");
            (new ResetPasswordView())->render();
            return;
        }

        // Chargement du template email depuis la vue
        $subject = 'CyberCigales — Réinitialisation de votre mot de passe';
        $resetLink = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        $templatePath = __DIR__ . '/../../Views/User/ResetPassword/reset-email.html';
        $message = file_get_contents($templatePath);
        $message = str_replace('{{RESET_LINK}}', $resetLink, $message);

        // Envoi du mail
        $this->mail->setFrom('cybercigales@gmail.com', 'CyberCigales');
        $this->mail->addAddress($usersEmail);
        $this->mail->addReplyTo('no-reply@cybercigales.fr', 'CyberCigales');
        $this->mail->XMailer = ' ';

        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->AltBody =
            "Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte CyberCigales.\n\n" .
            "Lien (valable 30 min) : {$url}\n\n" .
            "Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail.";

        $this->mail->send();

        // OWASP A09 - Logging : on log les demandes de réinitialisation (email tronqué pour la vie privée)
        \helpers\SecurityLogger::log('PASSWORD_RESET_REQUESTED', ['email' => substr($usersEmail, 0, 3) . '***']);

        // Message identique pour tous les cas (sécurité)
        flash("reset", "Si cette adresse email est associée à un compte, vous recevrez un email de réinitialisation.", 'form-message form-message-green');
        (new ResetPasswordView())->render();
    }
}
