<?php
namespace Controllers\User;
use Controllers\AbstractController;
use Models\User\User;
use Views\User\Login\LoginView;
use Attributes\Route;

/**
 * Contrôleur d'authentification utilisateur
 * 
 * Gère l'affichage du formulaire de connexion et le traitement de l'authentification
 * avec validation CAPTCHA et vérification des credentials.
 */
#[Route('/user/login', name: 'login')]
class Login extends AbstractController
{
    private User $userModel;

    /**
     * Constructeur - Initialise le modèle User
     * 
     * @return void
     */
    public function __construct() {
        // Je crée une instance de ma classe User pour pouvoir faire des opérations en BDD
        // Maintenant je peux utiliser $this->userModel partout dans ma classe pour :
        // - Insérer un nouvel utilisateur
        // - Vérifier si un email existe déjà
        // - Récupérer un utilisateur pour le login
        // - etc.
        $this->userModel = new User;
    }

    /**
     * Affiche le formulaire de connexion
     * 
     * @return void
     */
    function getMethod(){
        // On crée une instance de la vue "LoginView"
        $view = new LoginView();
        // On affiche le contenu de la page de connexion (formulaire login)
        $view->render();
    }
    
    /**
     * Crée une session utilisateur après authentification réussie
     * 
     * Enregistre les données utilisateur en session et redirige vers la page d'accueil.
     * 
     * @param object $user Objet utilisateur avec id, email, pseudo
     * @return void
     */
    public function createUserSession($user): void
    {
        // Je crée des variables de session avec les infos de l'utilisateur
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_pseudo'] = $user->pseudo;
        // Je redirige vers la page d'accueil ou le tableau de bord
        redirect("/");
    }
    
    /**
     * Traite la soumission du formulaire de connexion
     * 
     * Valide les données, vérifie le CAPTCHA, authentifie l'utilisateur
     * et crée une session en cas de succès.
     * 
     * @return void
     */
    function postMethod(): void
    {
        // Je nettoie TOUTES les données POST en une seule fois
        $_POST = filter_input_array(INPUT_POST);

        // Je récupère et nettoie les données du formulaire de connexion
        $data = [
            'name/email' => trim($_POST['name/email']), // Pseudo ou email
            'password' => trim($_POST['password']), // Mot de passe
            'captcha_code' => isset($_POST['captcha_code']) ? trim($_POST['captcha_code']) : ''
        ];

        // Validation des inputs - je vérifie que tous les champs sont remplis
        if(empty($data['name/email']) || empty($data['password']) || empty($data['captcha_code'])) {
            flash('login', "Veuillez remplir tous les champs");
            $view = new LoginView();
            $view->render();
            exit();
        }

        // Validation Captcha
        if (!isset($_SESSION)) { session_start(); }
        $sessionCaptcha = isset($_SESSION['captcha']) ? $_SESSION['captcha'] : '';
        if (empty($sessionCaptcha) || strcasecmp($data['captcha_code'], $sessionCaptcha) !== 0) {
            flash('login', "Captcha invalide. Cliquez sur l'image pour le régénérer et réessayez.");
            $view = new LoginView();
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
                flash('login', "Utilisateur non trouvé");
                $view = new LoginView();
                $view->render();
            }
        } else{
            // Si l'utilisateur n'existe pas, j'affiche une erreur
            flash('login', "Utilisateur non trouvé");
            $view = new LoginView();
            $view->render();
        }
    }
}