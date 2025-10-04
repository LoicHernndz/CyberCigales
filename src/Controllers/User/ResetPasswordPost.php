<?php

namespace Controllers\User;

use Controllers\ControllerInterface;
use Views\User\ResetPasswordView;

class ResetPasswordPost implements ControllerInterface
{
    function control(){
        $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $usersEmail = trim($_POST['usersEmail']);

        if(empty($usersEmail)){
            flash("reset", "SVP rentrée un email");
            $view = new ResetPasswordView();
            $view->render();
            exit();
        }

        if(!filter_var($usersEmail, FILTER_VALIDATE_EMAIL)){
            flash("reset", "Email invalide");
            $view = new ResetPasswordView();
            $view->render();
            exit();
        }

        //Sera utilisé pour interroger l'utilisateur à partir de la base de données.
        $selector = bin2hex(random_bytes(8));
        // sera utilisé pour confirmation une fois que l'entrée dans la base de données aura été trouvée
        $token = random_bytes(32);
        $url = "https://benahmed.alwaysdata.net/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);
        // Expire au bout de 30 minutes
        $expires = date("U") + 1800;
        if(!$this->resetModel->deleteEmail($usersEmail)){
            die ("There was an error");
        }
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        if(!$this->resetModel->insertToken($usersEmail, $selector, $hashedToken, $expires)){
            die ("There was an error");
        }
        //Envoyer l'email
        $subject = 'Réinitialisation de votre mot de passe';
        $message = '<p>Nous avons reçu une demande de réinitialisation de mot de passe. Le lien pour réinitialiser votre mot de passe est le suivant : </p>';
        $message .= '<p>Voici votre lien de réinitialisation : </br>';
        $message .= '<a href="' . $url . '">' . $url . '</a</p>';

        $this->mail->setFrom('cybercigales@gmail.com', 'CyberCigales');
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->addAddress($usersEmail);

        $this->mail->send();

        flash("reset", "Un email de réinitialisation a été envoyé !", 'form-message form-message-green');
        $view = new ResetPasswordView();
        $view->render();
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/reset-password" && $method === "POST";
    }
}