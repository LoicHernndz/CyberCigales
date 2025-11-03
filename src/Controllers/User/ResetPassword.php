<?php

namespace Controllers\User;

use config\EnvVar;
use Controllers\AbstractController;
use Models\User\ResetPasswords;
use Models\User\User;
use PHPMailer\src\PHPMailer;
use Views\User\ResetPassword\ResetPasswordView;

class ResetPassword extends AbstractController
{
    private ResetPasswords $resetModel;
    private User $userModel;
    private PHPMailer $mail;

    public function getMethod()
    {
        // Création d’une instance de la vue "ResetPasswordView"
        $view = new ResetPasswordView();
        // Affichage du contenu de la page (formulaire pour demander un lien de réinitialisation)
        $view->render();
    }

    public function postMethod()
    {
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
        $url = "https://cybercigales.fr/user/new-password?selector=" . $selector . "&validator=" . bin2hex($token);
        $expires = strval((int)(date("U")) + 1800);

        if (!$this->resetModel->deleteEmail($usersEmail)) {
            die("Erreur interne (deleteEmail)");
        }

        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        if (!$this->resetModel->insertToken($usersEmail, $selector, $hashedToken, $expires)) {
            die("Erreur interne (insertToken)");
        }

        // ---------------------------
        // EMAIL HTML (fond adaptable)
        // ---------------------------

        $subject = 'CyberCigales — Réinitialisation de votre mot de passe';
        $resetLink = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        $message = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="color-scheme" content="light dark">
  <title>Réinitialisation du mot de passe</title>
  <style>
    @media (prefers-color-scheme: light) {
      body { background: #ffffff !important; color: #0a0e27 !important; }
      a.button { background:#00ff41 !important; color:#0a0e27 !important; border-color:#00ff41 !important; }
      a.link { color:#006600 !important; }
      h1, .accent { color:#008f2d !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#0a0e27;color:#00ff41;font-family:'Courier New',monospace;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    Lien valable 30 minutes. Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.
  </div>

  <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width:640px;margin:0 auto;">
    <tr>
      <td style="padding:24px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" 
               style="background:linear-gradient(135deg,#0a0e27 0%,#1a1a2e 50%,#0f0f1e 100%);
                      border:2px solid #00ff41;border-radius:10px;">
          <tr>
            <td style="padding:28px 24px 8px 24px;text-align:center;">
              <div style="font-size:14px;letter-spacing:2px;text-transform:uppercase;color:#00ff41;">
                &gt; CyberCigales
              </div>
              <h1 style="margin:12px 0 0 0;font-size:22px;line-height:1.35;color:#00ff41;
                         text-shadow:0 0 6px #00ff41;">
                Réinitialisation de votre mot de passe
              </h1>
            </td>
          </tr>

          <tr>
            <td style="padding:16px 24px 0 24px;color:#00ffff;font-size:15px;line-height:1.55;">
              <p style="margin:0 0 12px 0;">
                Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.
              </p>
              <p style="margin:0 0 16px 0;">
                Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe. 
                <strong style="color:#00ff41;">Le lien expire dans 30 minutes.</strong>
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:8px 24px 4px 24px;text-align:center;">
              <a href="{$resetLink}" 
                 class="button"
                 style="display:inline-block;padding:12px 22px;border:2px solid #00ff41;border-radius:8px;
                        color:#00ff41;text-decoration:none;text-transform:uppercase;letter-spacing:2px;
                        font-weight:bold;">
                &gt; Réinitialiser le mot de passe
              </a>
            </td>
          </tr>

          <tr>
            <td style="padding:12px 24px 0 24px;color:#00ffff;font-size:13px;line-height:1.6;">
              <p style="margin:0 0 10px 0;">Si le bouton ne fonctionne pas, copiez ce lien :</p>
              <p style="margin:0;word-break:break-all;">
                <a href="{$resetLink}" class="link" style="color:#00ffff;text-decoration:underline;">{$resetLink}</a>
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:18px 24px 0 24px;color:#00ffff;font-size:13px;line-height:1.6;">
              <p style="margin:0 0 10px 0;">
                Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail ; 
                votre mot de passe restera inchangé.
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:22px 24px 24px 24px;">
              <hr style="border:none;border-top:2px solid rgba(0,255,65,.3);margin:0 0 14px 0;">
              <p style="margin:0;font-size:12px;color:#00ff41;text-transform:uppercase;letter-spacing:1px;text-align:center;">
                © CyberCigales — Sécurité &amp; cybersécurité
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

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

        // Message identique pour tous les cas (sécurité)
        flash("reset", "Si cette adresse email est associée à un compte, vous recevrez un email de réinitialisation.", 'form-message form-message-green');
        (new ResetPasswordView())->render();
    }
}
