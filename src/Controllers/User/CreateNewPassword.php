<?php

namespace Controllers\User;

use Controllers\ControllerInterface;
use Views\User\ResetPasswordView;

class CreateNewPassword implements ControllerInterface
{

    function control(){
        $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $data = [
            'selector' => trim($_POST['selector']),
            'validator' => trim($_POST['validator']),
            'pwd' => trim($_POST['pwd']),
            'pwd-repeat' => trim($_POST['pwd-repeat'])
        ];
        $url = 'https://benahmed.alwaysdata.net/create-new-password.php?selector=' . $data['selector'] .
            '&validator=' . $data['validator'];

        if(empty($_POST['pwd']) || empty($_POST['pwd-repeat'])){
            flash("newpwd", "SVP remplissez tous les champs");
            redirect($url);
        } else if($data['pwd'] != $data['pwd-repeat']){
            flash("newReset", "Les mots de passe ne correspondent pas");
            redirect($url);
        } else if(strlen($data['pwd']) < 6){
            flash("newReset", "Le mot de passe doit contenir au moins 6 caractères");
            redirect($url);
        }

        $currentDate = date("U");
        if(!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)){
            flash("newReset", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        $tokenBin = hex2bin($data['validator']);
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken);
        if(!$tokenCheck){
            flash("newReset", "Vous devez renvoyer une nouvelle demande de réinitialisation de mot de passe.");
            redirect($url);
        }

        $tokenEmail = $row->pwdResetEmail;
        if(!$this->userModel->findUserByEmailOrUsername($tokenEmail, $tokenEmail)){
            flash("newReset", "Il n'y a pas d'utilisateur avec cet email.");
            redirect($url);
        }

        $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);
        if(!$this->userModel->resetPassword($newPwdHash, $tokenEmail)){
            flash("newReset", "Il y a eu une erreur.");
            redirect($url);
        }

        if(!$this->resetModel->deleteEmail($tokenEmail)){
            flash("newReset", "Il y a eu une erreur.");
            redirect($url);
        }

        flash("newReset", "Votre mot de passe a été mis à jour ! Vous pouvez vous connecter avec votre nouveau mot de passe.", 'form-message form-message-green');
        redirect("/user/login");
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/new-password" && $method === "POST";
    }
}