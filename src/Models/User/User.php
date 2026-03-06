<?php

namespace Models\User;
use config\Database;

/**
 * Modèle utilisateur
 * 
 * Gère toutes les opérations CRUD sur les utilisateurs (inscription, connexion, mise à jour profil, scores).
 */
class User{
    private $db;

    /**
     * Constructeur - Initialise la connexion à la base de données
     */
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
    public function signup($data){

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
        // OWASP A02 - Cryptographic Failures : on ne log JAMAIS les hash de mots de passe
        // ni la structure des requêtes SQL (les anciennes lignes de debug ont été supprimées)
        // car un attaquant ayant accès aux logs pourrait exploiter ces informations
        $this->db->query('UPDATE users SET password_hash=:pwd WHERE TRIM(LOWER(email))=TRIM(LOWER(:email))');
        $this->db->bind(':pwd', $newPwdHash);
        $this->db->bind(':email', $tokenEmail);

        $result = $this->db->execute();
        // OWASP A09 - Logging : log sécurisé sans données sensibles (email tronqué)
        error_log('[SECURITY] Mot de passe réinitialisé pour : ' . substr($tokenEmail, 0, 3) . '***');
        if($result){
            return true;
        }else{
            return false;
        }
    }

    // Ajoute des points au score d'un utilisateur
    public function ajouterScore(int $userId, int $points): bool
    {
        $this->db->query('UPDATE users SET score = score + :points WHERE id = :id');
        $this->db->bind(':points', $points);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    // Récupère un utilisateur par son ID
    public function findUserById(int $userId)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        $row = $this->db->single();
        
        if($row){
            return $row;
        }else{
            return false;
        }
    }

    // Met à jour les informations d'un utilisateur
    public function updateUserInfo(int $userId, array $data): bool
    {
        // Récupérer l'ancien email
        $user = $this->findUserById($userId);
        if (!$user) {
            return false;
        }
        
        $oldEmail = $user->email;
        $newEmail = $data['email'];
        
        // Si l'email change, supprimer les anciens tokens de réinitialisation
        // pour éviter les problèmes de contrainte de clé étrangère
        if ($oldEmail !== $newEmail) {
            try {
                $this->db->query('DELETE FROM pwdreset WHERE pwdResetEmail = :email');
                $this->db->bind(':email', $oldEmail);
                $this->db->execute();
            } catch (\Exception $e) {
                // Si la table n'existe pas, ce n'est pas grave
                error_log('Avertissement: impossible de supprimer les tokens de réinitialisation: ' . $e->getMessage());
            }
        }
        
        // Maintenant on peut mettre à jour l'utilisateur en toute sécurité
        $this->db->query('UPDATE users 
                         SET prenom = :prenom, 
                             nom = :nom, 
                             pseudo = :pseudo, 
                             email = :email 
                         WHERE id = :id');
        
        $this->db->bind(':prenom', $data['prenom']);
        $this->db->bind(':nom', $data['nom']);
        $this->db->bind(':pseudo', $data['pseudo']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    public function deleteProfil(int $userId)
    {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }
}
?>