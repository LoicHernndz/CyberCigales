<?php

namespace Controllers\User;

use Controllers\ControllerInterface;
use Models\User\User;
use Models\User\ResetPasswords;

use PHPMailer\src\PHPMailer;
use PHPMailer\src\Exception;
use PHPMailer\src\SMTP;

class CreateNewPasswordPost implements ControllerInterface
{
    private $resetModel;
    private $userModel;
    private $mail;

    public function __construct()
    {
        $this->resetModel = new ResetPasswords;
        $this->userModel = new User;
        // Set up PHPMailer
        $this->mail = new PHPMailer();
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->SMTPAuth = true;
        $this->mail->Username = "cybercigales@gmail.com";
        $this->mail->Password = "megr wvzc czjy iejh ";
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
    }
    function control(){
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

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/new-password" && $method === "POST";
    }
}