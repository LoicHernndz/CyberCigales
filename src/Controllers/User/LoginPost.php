<?php
namespace Controllers\User;
use Controllers\ControllerInterface ;
use Models\User\User;
use Views\User\LoginView;

class LoginPost
{
    private User $userModel;

    // Le constructeur qui se lance automatiquement dès que je crée un objet Users
    public function __construct() {
        // Je crée une instance de ma classe User pour pouvoir faire des opérations en BDD
        // Maintenant je peux utiliser $this->userModel partout dans ma classe pour :
        // - Insérer un nouvel utilisateur
        // - Vérifier si un email existe déjà
        // - Récupérer un utilisateur pour le login
        // - etc.
        $this->userModel = new User;
    }
    public function createUserSession($user): void
    {
        // Je crée des variables de session avec les infos de l'utilisateur
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_pseudo'] = $user->pseudo;
        // Je redirige vers la page d'accueil ou le tableau de bord
        redirect("/");
    }
    function control(): void
    {
        // Je nettoie TOUTES les données POST en une seule fois
        $_POST = filter_input_array(INPUT_POST);

        // Je récupère et nettoie les données du formulaire de connexion
        $data = [
            'name/email' => trim($_POST['name/email']), // Pseudo ou email
            'password' => trim($_POST['password']) // Mot de passe
        ];

        // Validation des inputs - je vérifie que tous les champs sont remplis
        if(empty($data['name/email']) || empty($data['password'])) {
            $view = new LoginView("Veuillez remplir tous les champs");
            $view->render();
            exit();
        }

        // Je vérifie si l'utilisateur existe en base (par email ou pseudo)
        if($this->userModel->findUserByEmailOrUsername($data['name/email'], $data['name/email'])){
            // Si l'utilisateur existe, je récupère ses infos
            $loggedInUser = $this->userModel->login($data['name/email'], $data['password']);
            if($loggedInUser){
                // Si le mot de passe est correct, je crée une session utilisateur
                $this->createUserSession($loggedInUser);
            } else{
                // Si le mot de passe est incorrect, j'affiche une erreur
                $view = new LoginView("Utilisateur non trouvé");
                $view->render();
            }
        } else{
            // Si l'utilisateur n'existe pas, j'affiche une erreur
            $view = new LoginView("Utilisateur non trouvé");
            $view->render();
        }
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/login" && $method === "POST";
    }
}