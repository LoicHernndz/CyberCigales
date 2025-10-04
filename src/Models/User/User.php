<?php

namespace Models\User;
use config\Database;

require_once '../../config/Database.php';

class User{
    private $db;

    // Le constructeur qui se lance automatiquement quand je crée un objet User
    public function __construct()
    {
        // Je crée une instance de ma classe Database pour pouvoir faire des requêtes
        $this->db = new Database;
    }

    // Ma méthode pour vérifier si un email ou un pseudo existe déjà en base
    // Je l'utilise avant l'inscription pour éviter les doublons
    public function findUserByEmailOrUsername($email, $username){
        // Je cherche un utilisateur avec soit ce pseudo, soit cet email
        $this->db->query('SELECT * FROM users WHERE pseudo = :username OR email = :email');
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);

        // Je récupère le résultat (un utilisateur ou null)
        $row = $this->db->single();

        // Si j'ai trouvé un utilisateur, je le retourne
        // Sinon je retourne false (email et pseudo disponibles)
        if($row){
            return $row; // Utilisateur trouvé = email/pseudo déjà pris
        }else{
            return false; // Aucun utilisateur = email/pseudo libres
        }
    }


    // Ma méthode pour insérer un nouvel utilisateur en base de données
    public function register($data){

        // Ma requête d'insertion avec tous les champs nécessaires
        $this->db->query('INSERT INTO users (prenom, nom, pseudo, email, password_hash) 
        VALUES (:prenom, :nom, :pseudo, :email, :password_hash)');

        // Je lie chaque valeur à son placeholder
        $this->db->bind(':prenom', $data['prenom']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':pseudo', $data['pseudo']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', $data['password']);

        // J'exécute la requête et je retourne le résultat
        if($this->db->execute()){
            return true; // Inscription réussie
        } else{
            return false; // Erreur lors de l'inscription
        }
    }


    public function login($nameOrEmail, $password){
        $row = $this->findUserByEmailOrUsername($nameOrEmail, $nameOrEmail);
        if($row == false) {
            return false; // Utilisateur non trouvé
        }
        $hashedPassword = $row->password_hash;
        if(password_verify($password, $hashedPassword)){
            return $row; // Mot de passe correct, retourne les infos utilisateur
        } else {
            return false; // Mot de passe incorrect
        }
    }


    //Réinitialisation du mot de passe
    public function resetPassword($newPwdHash, $tokenEmail){
        error_log('DEBUG: requête SQL = UPDATE users SET password_hash=' . $newPwdHash . ' WHERE TRIM(LOWER(email))=TRIM(LOWER(' . $tokenEmail . '))');
        $this->db->query('UPDATE users SET password_hash=:pwd WHERE TRIM(LOWER(email))=TRIM(LOWER(:email))');
        $this->db->bind(':pwd', $newPwdHash);
        $this->db->bind(':email', $tokenEmail);

        $result = $this->db->execute();
        error_log('DEBUG: rows updated = ' . $this->db->rowCount());
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
?>