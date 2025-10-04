<?php

namespace Controllers\User;

use Models\User\User;

class Users {
    // Je stocke mon modèle User pour pouvoir l'utiliser dans toutes mes méthodes de cette classe
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

    // Ma méthode pour gérer l'inscription d'un nouvel utilisateur
    public function register(){
        // Je nettoie TOUTES les données POST en une seule fois
        // FILTER_SANITIZE_STRING va :
        // - Enlever les balises HTML (<script>, <img>, etc.)
        // - Supprimer les caractères dangereux
        // - Protéger contre les attaques XSS (cross-site scripting)
        // Exemple : si quelqu'un tape "<script>alert('hack')</script>" dans le prénom,
        // ça devient juste "scriptalert('hack')script" (inoffensif)
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Je récupère et nettoie toutes les données du formulaire d'inscription
        $data = [
            'prenom' => trim($_POST['prenom']), // Je récupère le prénom et j'enlève les espaces
            'nom' => trim($_POST['nom']), // Je récupère le nom et j'enlève les espaces
            'pseudo' => trim($_POST['pseudo']), // Je récupère le pseudo et j'enlève les espaces
            'email' => trim($_POST['email']), // Je récupère l'email et j'enlève les espaces
            'password' => trim($_POST['password']), // Je récupère le mot de passe
            'password_repeat' => trim($_POST['password_repeat']) // Je récupère la confirmation du mot de passe
        ];

        // Validation des inputs - je vérifie que tous les champs sont remplis
        if(empty($data['prenom']) || empty($data['nom']) || empty($data['pseudo']) || empty($data['email']) || empty($data['password']) || empty($data['password_repeat'])) {
            flash("register", "Veuillez remplir tous les champs");
            redirect("/signup.php");
        }

        // Je vérifie si le pseudo contient seulement des lettres et des chiffres (pas d'espaces, pas de caractères spéciaux)
        // preg_match avec "/^[a-zA-Z0-9]*$/" = du début à la fin, que des lettres minuscules, majuscules et chiffres
        if(!preg_match("/^[a-zA-Z0-9]*$/", $data['pseudo'])){
            // Si le pseudo contient des trucs bizarres (espaces, @, !, etc.), j'affiche une erreur
            flash("register", "Pseudo Invalide");
            redirect("/signup.php"); // Je renvoie l'utilisateur sur la page d'inscription
        }

        // Je vérifie si l'email a un format valide (doit contenir @ et un domaine)
        // FILTER_VALIDATE_EMAIL vérifie automatiquement si c'est un vrai format d'email
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            // Si l'email n'a pas le bon format (pas de @, domaine invalide, etc.)
            flash("register", "Email invalide");
            redirect("/signup.php"); // Je renvoie l'utilisateur pour corriger
        }


        // Je vérifie que le mot de passe fait au moins 6 caractères
        if(strlen($data['password']) < 6){
            // Si le mot de passe est trop court, c'est pas sécurisé
            flash("register", "Mot de passe invalide");
            redirect("../signup.php");
        } else if($data['password'] !== $data['password_repeat']){
            // Je vérifie que les deux mots de passe tapés sont identiques
            // Si l'utilisateur s'est trompé en retapant son mot de passe
            flash("register", "Les mots de passe ne correspondent pas");
            redirect("../signup.php");
        }


        // Je vérifie si quelqu'un utilise déjà cet email ou ce pseudo
        // Ma méthode findUserByEmailOrUsername cherche dans la base s'il existe déjà
        if($this->userModel->findUserByEmailOrUsername($data['email'], $data['pseudo'])){
            // Si quelqu'un a déjà pris cet email ou ce pseudo
            flash("register", "Pseudo/Email est déja pris");
            redirect("../signup.php");
        }

        // Tout est bon ! Je hash le mot de passe pour le sécuriser avant de le stocker en base
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // J'essaie de créer l'utilisateur en base de données
        if($this->userModel->register($data)){
            // Si ça marche, je redirige vers la page de connexion
            redirect("../login.php");
        } else{
            // Si ça plante (problème de base, etc.), j'arrête tout et j'affiche l'erreur
            die("Quelque chose s'est mal passé");
        }
    }

    public function logout(){
        // Je supprime la variable de session qui contient l'id de l'utilisateur
        unset($_SESSION['user_id']);
        // Je supprime la variable de session qui contient l'email de l'utilisateur
        unset($_SESSION['user_email']);
        // Je supprime la variable de session qui contient le pseudo de l'utilisateur
        unset($_SESSION['user_pseudo']);
        // Je détruis complètement la session (toutes les variables de session sont supprimées)
        session_destroy();
        // Je redirige l'utilisateur vers la page d'accueil
        redirect("../index.php");
    }
}


// Je crée une instance de ma classe Users pour pouvoir utiliser ses méthodes
$init = new Users;

// Je vérifie si quelqu'un a envoyé des données via un formulaire (méthode POST)
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Je regarde quel type d'action l'utilisateur veut faire
    switch($_POST['type']){
        case 'register': // Si c'est une inscription
            $init->register(); // J'appelle ma méthode register()
            break;
        default:  // Si ce n'est ni register ni login, je redirige vers l'accueil
            redirect("../index.php");
    }
}else{
    // Si la requête n'est pas POST, je regarde le paramètre 'q' dans l'URL
    switch($_GET['q']){
        case 'logout':
            $init->logout(); // Si q=logout, je déconnecte l'utilisateur
            break;
        default:
            redirect("../index.php"); // Sinon, je redirige vers l'accueil
    }
}
?>