<?php

namespace Models\User;

use config\Database;

/**
 * Modèle de gestion des tokens de réinitialisation de mot de passe
 * 
 * Gère les opérations CRUD sur la table pwdreset pour les demandes de réinitialisation.
 */
class ResetPasswords
{
    /** @var Database Instance de connexion à la base de données */
    private Database $db;

    /**
     * Constructeur - Initialise la connexion à la base de données
     */
    public function __construct()
    {
        $this->db = new Database;
    }

    // 🔹 Méthode : supprime les anciens tokens de réinitialisation associés à un e-mail
    public function deleteEmail($email)
    {
        // Prépare la requête SQL pour supprimer un enregistrement selon l’adresse e-mail
        $this->db->query('DELETE FROM pwdreset WHERE pwdResetEmail = :email');
        // Lie la valeur du paramètre :email
        $this->db->bind(':email', $email);

        // Exécute la requête et retourne true si elle réussit, false sinon
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // 🔹 Méthode : insère un nouveau token de réinitialisation dans la table "pwdreset"
    public function insertToken($email, $selector, $hashedToken, $expires)
    {
        // Prépare la requête SQL d’insertion
        $this->db->query('INSERT INTO pwdreset (pwdResetEmail, pwdResetSelector, pwdResetToken, 
        pwdResetExpires) VALUES (:email, :selector, :token, :expires)');
        // Lie chaque paramètre à sa valeur
        $this->db->bind(':email', $email);
        $this->db->bind(':selector', $selector);
        $this->db->bind(':token', $hashedToken);
        $this->db->bind(':expires', $expires);

        // Exécute la requête et retourne true si tout s’est bien passé
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // 🔹 Méthode : récupère un token valide pour un utilisateur donné
    public function resetPassword($selector, $currentDate)
    {
        // Prépare une requête SQL pour trouver un token correspondant au selector
        // et dont la date d’expiration est encore valide
        $this->db->query('SELECT * FROM pwdreset WHERE pwdResetSelector = :selector AND 
        pwdResetExpires >= :currentDate');
        // Lie les valeurs aux paramètres
        $this->db->bind(':selector', $selector);
        $this->db->bind(':currentDate', $currentDate);

        // Récupère une seule ligne de résultat
        $row = $this->db->single();

        // Si une ligne est trouvée, la retourne, sinon retourne false
        if ($row) {
            return $row;
        } else {
            return false;
        }
    }
}
