<?php
namespace Controllers\User;
use Controllers\AbstractController;
use Models\User\User;
use Views\User\Signup\SignupView;
use Attributes\Route;

/**
 * Contrôleur d'inscription utilisateur
 *
 * Gère l'enregistrement d'un nouvel utilisateur avec validation complète des données,
 * vérification d'unicité (email/pseudo) et hashage sécurisé du mot de passe.
 */
#[Route('/user/signup', name: 'user_signup')]
class Signup extends AbstractController
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
     * Affiche le formulaire d'inscription
     * 
     * @return void
     */
    function getMethod(){
        // Création d’une instance de la vue d’inscription
        $view = new SignupView();
        // Affichage de la page (formulaire d’inscription)
        $view->render();
    }
    
    /**
     * Traite la soumission du formulaire d'inscription
     * 
     * Valide toutes les données (format email, longueur mot de passe, unicité pseudo/email),
     * vérifie le CAPTCHA, hashe le mot de passe et crée l'utilisateur en base de données.
     * 
     * @return void
     */
    function postMethod(){
        // OWASP A01 : vérification CSRF
        $this->csrfVerify();

        $_POST = filter_input_array(INPUT_POST);

        $data = [
            'prenom' => strip_tags(trim($_POST['prenom'])),
            'nom' => strip_tags(trim($_POST['nom'])),
            'pseudo' => trim($_POST['pseudo']),
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'password_repeat' => trim($_POST['password_repeat']),
            'accept_mentions' => isset($_POST['accept_mentions']) ? $_POST['accept_mentions'] : '',
            'captcha_code' => isset($_POST['captcha_code']) ? trim($_POST['captcha_code']) : ''
        ];

        // Validation des inputs - je vérifie que tous les champs sont remplis
        if(empty($data['prenom']) || empty($data['nom']) || empty($data['pseudo']) || empty($data['email']) || empty($data['password']) || empty($data['password_repeat']) || empty($data['captcha_code'])) {
            flash("signup", "Veuillez remplir tous les champs");
            $view = new SignupView();
            $view->render();
            exit();
        }

        // Validation Captcha
        if (!isset($_SESSION)) { session_start(); }
        $sessionCaptcha = isset($_SESSION['captcha']) ? $_SESSION['captcha'] : '';
        if (empty($sessionCaptcha) || strcasecmp($data['captcha_code'], $sessionCaptcha) !== 0) {
            flash("signup", "Captcha invalide. Cliquez sur l'image pour le régénérer et réessayez.");
            $view = new SignupView();
            $view->render();
            exit();
        }

        // Vérification de l'acceptation des mentions légales
        if($data['accept_mentions'] !== '1'){
            flash("signup", "Vous devez accepter les mentions légales pour vous inscrire");
            $view = new SignupView();
            $view->render();
            exit();
        }

        // Je vérifie si le pseudo contient seulement des lettres et des chiffres (pas d'espaces, pas de caractères spéciaux)
        // preg_match avec "/^[a-zA-Z0-9]*$/" = du début à la fin, que des lettres minuscules, majuscules et chiffres
        if(!preg_match("/^[a-zA-Z0-9]*$/", $data['pseudo'])){
            // Si le pseudo contient des trucs bizarres (espaces, @, !, etc.), j'affiche une erreur
            flash("signup", "Pseudo Invalide");
            $view = new SignupView(); // Je renvoie l'utilisateur sur la page d'inscription
            $view->render();
            exit();
        }

        // Je vérifie si l'email a un format valide (doit contenir @ et un domaine)
        // FILTER_VALIDATE_EMAIL vérifie automatiquement si c'est un vrai format d'email
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            // Si l'email n'a pas le bon format (pas de @, domaine invalide, etc.)
            flash("signup", "Email invalide");
            $view = new SignupView(); // Je renvoie l'utilisateur pour corriger
            $view->render();
            exit();
        }


        // OWASP A07 : validation mot de passe renforcée (12 chars, majuscule, minuscule, chiffre)
        if(strlen($data['password']) < 12){
            flash("signup", "Le mot de passe doit contenir au moins 12 caractères");
            $view = new SignupView();
            $view->render();
            exit();
        } else if(!preg_match('/[A-Z]/', $data['password']) || !preg_match('/[a-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])){
            flash("signup", "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre");
            $view = new SignupView();
            $view->render();
            exit();
        } else if($data['password'] !== $data['password_repeat']){
            flash("signup", "Les mots de passe ne correspondent pas");
            $view = new SignupView();
            $view->render();
            exit();
        }


        // Je vérifie si quelqu'un utilise déjà cet email ou ce pseudo
        // Ma méthode findUserByEmailOrUsername cherche dans la base s'il existe déjà
        if($this->userModel->findUserByEmailOrUsername($data['email'], $data['pseudo'])){
            // Si quelqu'un a déjà pris cet email ou ce pseudo
            flash("signup", "Pseudo/Email est déja pris");
            $view = new SignupView();
            $view->render();
            exit();
        }

        // Tout est bon ! Je hash le mot de passe pour le sécuriser avant de le stocker en base
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // J'essaie de créer l'utilisateur en base de données
        if($this->userModel->signup($data)){
            // Si ça marche, je redirige vers la page de connexion
            redirect(url('user_login'));
        } else{
            flash("signup", "Une erreur est survenue lors de l'inscription. Veuillez réessayer.");
            $view = new SignupView();
            $view->render();
        }
    }
}