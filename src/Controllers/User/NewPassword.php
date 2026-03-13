<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\ResetPasswords;
use Models\User\User;
use PHPMailer\src\PHPMailer;
use Views\User\CreateNewPassword\CreateNewPasswordView;
use Attributes\Route;

/**
 * Contrôleur de création d'un nouveau mot de passe
 *
 * Gère le processus de réinitialisation de mot de passe :
 * valide le token (GET) et effectue le changement de mot de passe (POST).
 */
#[Route('/user/new-password', name: 'user_new_password')]
class NewPassword extends AbstractController
{
    /**
     * Modèle de réinitialisation de mots de passe
     *
     * @var ResetPasswords|null
     */
    private $resetModel;

    /**
     * Modèle utilisateur
     *
     * @var User|null
     */
    private $userModel;

    /**
     * Instance PHPMailer pour l'envoi d'emails
     *
     * @var PHPMailer|null
     */
    private $mail;

    /**
     * Affiche le formulaire de création d'un nouveau mot de passe
     *
     * Valide le selector et le validator passés en paramètres GET.
     * Affiche un message d'erreur si les paramètres sont invalides.
     *
     * @return void
     */
    public function getMethod(): void
    {
        if (empty($_GET['selector']) || empty($_GET['validator'])) {
            flash('login', "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.");
            redirect(url('user_login'));
            return;
        }

        $selector = $_GET['selector'];
        $validator = $_GET['validator'];

        if (!ctype_xdigit($selector) || !ctype_xdigit($validator)) {
            flash('login', "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.");
            redirect(url('user_login'));
            return;
        }

        $view = new CreateNewPasswordView();
        $view->addTemplateKey('SELECTOR', $selector);
        $view->addTemplateKey('VALIDATOR', $validator);
        $view->render();
    }

    /**
     * Traite la soumission du nouveau mot de passe
     *
     * Vérifie le token de réinitialisation, valide le nouveau mot de passe,
     * met à jour en base de données et envoie un email de confirmation.
     *
     * @return void
     */
    public function postMethod(): void
    {
        // OWASP A01 : vérification CSRF
        $this->csrfVerify();

        $this->resetModel = new ResetPasswords;
        $this->userModel = new User;

        $envFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
        $envVars = [];

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0)
                    continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $envVars[trim($key)] = trim($value, '"\'');
                }
            }
        }

        $this->mail = new PHPMailer();
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = $envVars['SMTP_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $envVars['SMTP_USER'];
        $this->mail->Password = $envVars['SMTP_PASS'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $envVars['SMTP_PORT'];

        $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $data = [
            'selector' => trim($_POST['selector']),
            'validator' => trim($_POST['validator']),
            'pwd' => trim($_POST['pwd']),
            'pwd-repeat' => trim($_POST['pwd-repeat'])
        ];

        $url = rtrim(\config\EnvVar::get('APP_URL') ?? '', '/') . '/user/new-password?selector=' . $data['selector'] .
            '&validator=' . $data['validator'];

        if (empty($_POST['pwd']) || empty($_POST['pwd-repeat'])) {
            flash("new-password", "SVP remplissez tous les champs");
            redirect($url);
        } else if ($data['pwd'] != $data['pwd-repeat']) {
            flash("new-password", "Les mots de passe ne correspondent pas");
            redirect($url);
        } else if (strlen($data['pwd']) < 12) {
            flash("new-password", "Le mot de passe doit contenir au moins 12 caractères");
            redirect($url);
        } else if (!preg_match('/[A-Z]/', $data['pwd']) || !preg_match('/[a-z]/', $data['pwd']) || !preg_match('/[0-9]/', $data['pwd'])) {
            flash("new-password", "Le mot de passe doit contenir une majuscule, une minuscule et un chiffre");
            redirect($url);
        }

        $currentDate = date("U");
        if (!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)) {
            flash("new-password", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        $tokenBin = hex2bin($data['validator']);
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken);
        if (!$tokenCheck) {
            flash("new-password", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        $tokenEmail = $row->pwdResetEmail;

        if (!$this->userModel->findUserByEmailOrUsername($tokenEmail, $tokenEmail)) {
            flash("new-password", "Il n'y a pas d'utilisateur avec cet email.");
            redirect($url);
        }

        $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);

        if (!$this->userModel->resetPassword($newPwdHash, $tokenEmail)) {
            flash("new-password", "Il y a eu une erreur.");
            redirect($url);
        }

        if (!$this->resetModel->deleteEmail($tokenEmail)) {
            flash("new-password", "Il y a eu une erreur.");
            redirect($url);
        }

        // OWASP A09 : log changement de mot de passe
        \Helpers\SecurityLogger::log('PASSWORD_CHANGED', ['email' => substr($tokenEmail, 0, 3) . '***']);

        flash("login", "Votre mot de passe a été mis à jour ! Vous pouvez vous connecter avec votre nouveau mot de passe.", 'form-message form-message-green');
        redirect(url('user_login'));
    }
}
