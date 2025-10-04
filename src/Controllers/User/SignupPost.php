<?php
namespace Controllers\User;
use Controllers\ControllerInterface ;
use Models\User\User;
use Views\User\SignupView;
use Views\User\UserView;


class SignupPost implements ControllerInterface
{
    private User $userModel;

    // Le constructeur qui se lance automatiquement dès que je crée un objet Logout
    public function __construct() {
        // Je crée une instance de ma classe User pour pouvoir faire des opérations en BDD
        // Maintenant je peux utiliser $this->userModel partout dans ma classe pour :
        // - Insérer un nouvel utilisateur
        // - Vérifier si un email existe déjà
        // - Récupérer un utilisateur pour le login
        // - etc.
        $this->userModel = new User;
    }
    function control(){
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
            flash("signup", "Veuillez remplir tous les champs");
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


        // Je vérifie que le mot de passe fait au moins 6 caractères
        if(strlen($data['password']) < 6){
            // Si le mot de passe est trop court, c'est pas sécurisé
            flash("signup", "Mot de passe invalide (au moins 6 caracteres)");
            $view = new SignupView();
            $view->render();
            exit();
        } else if($data['password'] !== $data['password_repeat']){
            // Je vérifie que les deux mots de passe tapés sont identiques
            // Si l'utilisateur s'est trompé en retapant son mot de passe
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
            redirect("/user/login");
        } else{
            // Si ça plante (problème de base, etc.), j'arrête tout et j'affiche l'erreur
            die("Quelque chose s'est mal passé");
        }
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/signup" && $method === "POST";
    }
}