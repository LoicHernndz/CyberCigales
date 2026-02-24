<?php

namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\ResetPasswords;
use Models\User\User;
use PHPMailer\src\PHPMailer;
use Views\User\CreateNewPassword\CreateNewPasswordView;

class NewPassword extends AbstractController
{
    private $resetModel;
    private $userModel;
    private $mail;

    function getMethod(){
        if(empty($_GET['selector']) || empty($_GET['validator'])) {
            echo "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.";
        } else {
            $selector = $_GET['selector'];
            $validator = $_GET['validator'];
            if(ctype_xdigit($selector) && ctype_xdigit($validator)) {
                $view = new CreateNewPasswordView();
                $view->render();
            } else {
                echo "Nous ne pouvons pas valider votre demande de réinitialisation de mot de passe.";
            }
        }
    }

    public function postMethod()
    {
        $this->resetModel = new ResetPasswords;
        $this->userModel = new User;

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

        $url = 'https://benahmed.alwaysdata.net/user/new-password?selector=' . $data['selector'] .
            '&validator=' . $data['validator'];

        if(empty($_POST['pwd']) || empty($_POST['pwd-repeat'])){
            flash("new-password", "SVP remplissez tous les champs");
            redirect($url);
        } else if($data['pwd'] != $data['pwd-repeat']){
            flash("new-password", "Les mots de passe ne correspondent pas");
            redirect($url);
        } else if(strlen($data['pwd']) < 6){
            flash("new-password", "Le mot de passe doit contenir au moins 6 caractères");
            redirect($url);
        }

        $currentDate = date("U");
        if(!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)){
            flash("new-password", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        $tokenBin = hex2bin($data['validator']);
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken);
        if(!$tokenCheck){
            flash("new-password", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        $tokenEmail = $row->pwdResetEmail;

        if(!$this->userModel->findUserByEmailOrUsername($tokenEmail, $tokenEmail)){
            flash("new-password", "Il n'y a pas d'utilisateur avec cet email.");
            redirect($url);
        }

        $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);

        if(!$this->userModel->resetPassword($newPwdHash, $tokenEmail)){
            flash("new-password", "Il y a eu une erreur.");
            redirect($url);
        }

        if(!$this->resetModel->deleteEmail($tokenEmail)){
            flash("new-password", "Il y a eu une erreur.");
            redirect($url);
        }

        flash("login", "Votre mot de passe a été mis à jour ! Vous pouvez vous connecter avec votre nouveau mot de passe.", 'form-message form-message-green');
        redirect("/user/login");
    }
}
