<?php

namespace config;
use PDO;
use PDOException;

class Database
{
    // Mes paramètres de connexion à la base de données AlwaysData
    private string $host = 'mysql-benahmed.alwaysdata.net'; // L'adresse de mon serveur MySQL
    private string $user = 'benahmed'; // Mon nom d'utilisateur pour la BDD
    private string $pass = '5qAr)}gXVe!QG^-'; // Mon mot de passe (je sais il faudrait le cacher dans un fichier config)
    private string $dbname = 'benahmed_escape_game'; // Le nom de ma base de données pour le projet
    private $dbh; // Database Handler - c'est l'objet PDO qui va gérer ma connexion
    private $stmt; // Statement - ici je vais stocker mes requêtes préparées
    private $error; // Pour capturer les erreurs si la connexion foire


    public function __construct()
    {
        // Je construis mon DSN (Data Source Name) - c'est comme l'adresse complète de ma BDD
        // Le DSN dit à PDO : "Va te connecter sur ce serveur MySQL, dans cette base précise"
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        // Ça donne quelque chose comme : "mysql:host=mysql-benahmed.alwaysdata.net;dbname=benahmed_escape_game"

        // Mes options PDO pour optimiser ma connexion
        $options = array(
            // ATTR_PERSISTENT = true : Je garde ma connexion ouverte même après la fin du script
            // Ça évite de se reconnecter à chaque fois = plus rapide pour mon site
            // Parfait pour un site avec beaucoup d'utilisateurs qui se connectent souvent
            PDO::ATTR_PERSISTENT => true,

            // ATTR_ERRMODE = EXCEPTION : Si il y a une erreur SQL, ça lance une exception
            // Au lieu de juste retourner false, ça me donne des détails précis sur l'erreur
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // J'essaie de me connecter à ma base de données
        try {
            // Je crée mon objet PDO (ma connexion) avec tous mes paramètres
            // $dsn = l'adresse de ma BDD, $user = mon nom d'utilisateur, $pass = mon mot de passe
            // $options = mes réglages pour optimiser la connexion
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            // Si tout se passe bien, $this->dbh contient maintenant ma connexion active
            // Je peux maintenant faire des requêtes SQL avec cette connexion

        } // Si quelque chose se passe mal pendant la connexion, je capture l'erreur
        catch (PDOException $e) {

            // Je stocke le message d'erreur dans ma propriété $error
            // getMessage() me donne le détail exact de ce qui a foiré
            $this->error = $e->getMessage();

            // J'affiche l'erreur directement sur la page
            echo $this->error;
        }
    }


    // Ma méthode pour préparer une requête SQL avec des placeholders
    public function query($sql)
    {
        // prepare() c'est comme préparer un formulaire à trous
        // Au lieu d'écrire directement "SELECT * FROM users WHERE email = 'john@gmail.com'"
        // J'écris "SELECT * FROM users WHERE email = ?"
        // Le ? c'est un "trou" que je vais remplir plus tard de manière ultra-sécurisée

        // ANALOGIE SIMPLE :
        // C'est comme un formulaire papier où j'écris :
        // "Bonjour _______, votre commande de _______ est prête"
        // Les _______ sont mes "placeholders", je les remplirai après

        // POURQUOI C'EST SÉCURISÉ :
        // Sans prepare() : "SELECT * FROM users WHERE email = '" . $email . "'"
        // Si un hacker met comme email : ' OR 1=1 --
        // Ça donne : "SELECT * FROM users WHERE email = '' OR 1=1 --'"
        // Et là il récupère TOUS les utilisateurs ! (injection SQL)

        // Avec prepare() :
        // PDO va "nettoyer" automatiquement tout ce que je mets dans les ?
        // Même si un hacker essaie des trucs bizarres, ça sera traité comme du texte normal

        $this->stmt = $this->dbh->prepare($sql);
        // Maintenant ma requête est "préparée" = prête à recevoir des valeurs propres
    }

    // Ma méthode pour remplir les "trous" (?) de ma requête préparée
    public function bind($param, $value, $type = NULL)
    {
        // Si je n'ai pas dit quel type de données c'est, ma méthode va le deviner toute seule
        // C'est pratique, je n'ai pas besoin de me casser la tête à chaque fois
        if (is_null($type)) {
            // Je regarde le type de ma valeur pour dire à la base de données comment la traiter
            switch (true) {
                case is_int($value): // Si c'est un nombre entier (comme 123, 456, 0)
                    // Je dis à PDO : "Hé, traite ça comme un nombre, pas comme du texte"
                    $type = PDO::PARAM_INT;
                    // Exemple : user_id = 5 (le 5 sera traité comme un nombre)
                    break;

                case is_bool($value): // Si c'est true ou false
                    // PDO comprend pas les vrais booléens, alors je les transforme en nombres
                    // true devient 1, false devient 0
                    $type = PDO::PARAM_INT;
                    // Exemple : active = true → active = 1 dans la base
                    break;

                case is_null($value): // Si c'est null (= vide, rien)
                    $type = PDO::PARAM_NULL;
                    // Exemple : telephone = null (pas de numéro de téléphone)
                    break;

                default: // Dans tous les autres cas (texte, email, mot de passe, etc.)
                    // Je traite ça comme du texte normal
                    $type = PDO::PARAM_STR;
                // Exemple : email = "john@gmail.com" (du texte)
            }
        }

        // Maintenant je remplis mon "trou" avec la valeur et je dis à PDO quel type c'est
        // $param = quel trou je remplis (1er ?, 2ème ?, etc.)
        // $value = qu'est-ce que je mets dedans
        // $type = comment PDO doit traiter cette valeur
        $this->stmt->bindValue($param, $value, $type);


    }

    // Ma méthode pour lancer ma requête préparée
    public function execute()
    {
        // Ici je dis à PDO : "Maintenant que j'ai préparé ma requête et rempli tous les trous,
        // vas-y, exécute-la pour de vrai dans la base de données !"
        // Retourne true si tout s'est bien passé, false si ça a planté
        return $this->stmt->execute();

        // ANALOGIE : C'est comme appuyer sur "Envoyer" après avoir rempli un formulaire
        // Jusqu'ici j'ai juste préparé, maintenant j'envoie vraiment à la base
    }


    // Ma méthode pour récupérer PLUSIEURS résultats
    public function resultSet()
    {
        // D'abord j'exécute ma requête
        $this->execute();

        // Puis je récupère TOUS les résultats sous forme d'objets
        // PDO::FETCH_OBJ = chaque ligne devient un objet avec des propriétés
        // Parfait pour les requêtes qui renvoient plusieurs utilisateurs, plusieurs commandes, etc.
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);

        // EXEMPLE DE RETOUR :
        // [
        //   {id: 1, email: "paul@gmail.com", active: 1},
        //   {id: 2, email: "marie@gmail.com", active: 0},
        //   {id: 3, email: "jean@gmail.com", active: 1}
        // ]
    }


    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Ma méthode pour récupérer UN SEUL résultat
    public function single()
    {
        // D'abord j'exécute ma requête
        $this->execute();

        // Puis je récupère seulement le PREMIER résultat sous forme d'objet
        // Parfait pour vérifier si un utilisateur existe, récupérer un profil, etc.
        return $this->stmt->fetch(PDO::FETCH_OBJ);

        // EXEMPLE DE RETOUR :
        // {id: 1, email: "paul@gmail.com", password: "hash123", active: 1}
        // Ou null si aucun résultat trouvé
    }
}
