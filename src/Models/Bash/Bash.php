<?php

namespace Models\Bash;

use Exception;

/**
 * Modèle de l'environnement Bash simulé
 * 
 * Crée une arborescence de fichiers virtuelle pour le terminal simulé.
 */
class Bash
{
    /** @var array<int, string> Historique des commandes exécutées */
    private array $commandHistory;

    /** @var bool Indique si le terminal est connecté via SSH */
    private bool $isTerminalLoggedIn;

    /** @var File Racine du filesystem du hacker (accessible via SSH) */
    private File $root;

    /** @var File Racine du filesystem local du guest */
    private File $localRoot;

    /** @var array{user: string, pass: string} Identifiants de connexion locale */
    private array $login;

    /** @var array{user: string, host: string, pass: string} Identifiants SSH du hacker */
    private array $sshCredentials;


    /**
     * Constructeur - Initialise l'arborescence de fichiers virtuelle
     * 
     * @throws Exception
     */
    public function __construct()
    {
        $this->commandHistory = [];
        $this->isTerminalLoggedIn = false;
        $this->root = new File(null, 'home', 'dir', []);
        $this->login = ['user' => 'admin', 'pass' => 'secure'];

        // Identifiants SSH que le joueur doit découvrir pendant l'escape game
        $this->sshCredentials = [
            'user' => 'admin',
            'host' => '192.168.1.42',
            'pass' => 'Marseille13!'
        ];

        // ══════════════════════════════════════════════════════════════
        // FILESYSTEM LOCAL (machine du joueur / enqueteur)
        // Environnement principal utilise pour les enigmes de base,
        // les exercices de commandes et les metadonnees.
        // ══════════════════════════════════════════════════════════════
        $this->localRoot = new File(null, 'home', 'dir', []);

        // -- Mission / briefing --
        new File($this->localRoot, 'mission', 'dir', []);
        new File($this->localRoot, 'mission/briefing.txt', 'txt',
            "=== BRIEFING DE MISSION ===\n\n" .
            "Objectif : Enqueter sur un suspect lie a des activites malveillantes.\n" .
            "Un acces SSH a ete detecte sur l'adresse 192.168.1.42\n\n" .
            "Etapes :\n" .
            "1. Explorez les fichiers locaux pour vous familiariser avec le terminal\n" .
            "2. Analysez les preuves numeriques (metadonnees, fichiers suspects)\n" .
            "3. Trouvez les identifiants SSH du suspect\n" .
            "4. Connectez-vous a son serveur : ssh utilisateur@adresse_ip\n\n" .
            "Bonne chance, enqueteur."
        );
        new File($this->localRoot, 'mission/notes-enquete.txt', 'txt',
            "Notes de l'enqueteur :\n" .
            "- Le suspect utilise le pseudo 'admin' sur la plupart de ses comptes\n" .
            "- Il est tres attache a sa ville natale\n" .
            "- Son mot de passe contient probablement un chiffre et un caractere special\n" .
            "- Consultez les autres applications (Instagram, mails, agenda) pour plus d'indices"
        );
        new File($this->localRoot, 'mission/scan-reseau.txt', 'txt',
            "=== Resultat du scan reseau (nmap 192.168.1.0/24) ===\n\n" .
            "192.168.1.42 :\n" .
            "  Port 22 (SSH)   : OUVERT  [OpenSSH 8.9]\n" .
            "  Port 80 (HTTP)  : FERME\n" .
            "  Port 443 (HTTPS): FERME\n\n" .
            "Le service SSH est actif sur la machine cible.\n" .
            "Systeme detecte : Linux Ubuntu 22.04"
        );

        // -- Documents de travail --
        new File($this->localRoot, 'documents', 'dir', []);
        new File($this->localRoot, 'documents/cours-terminal.txt', 'txt',
            "=== Aide-memoire commandes terminal ===\n\n" .
            "ls         Lister les fichiers du dossier courant\n" .
            "cd <nom>   Se deplacer dans un dossier\n" .
            "cd ..      Revenir au dossier parent\n" .
            "cat <nom>  Afficher le contenu d'un fichier\n" .
            "pwd        Afficher le chemin du dossier actuel\n" .
            "clear      Effacer l'ecran du terminal\n" .
            "ssh u@ip   Se connecter a une machine distante"
        );
        new File($this->localRoot, 'documents/methode-brut-force.txt', 'txt',
            "La methode par brute force consiste a tester un tres grand nombre\n" .
            "de combinaisons de caracteres en esperant trouver le bon mot de passe.\n" .
            "Cela peut etre tres long.\n\n" .
            "Mais si on utilise des elements que l'on sait probablement presents\n" .
            "dans le mot de passe (ville, date, prenom...), le temps necessaire\n" .
            "pour le trouver s'en trouve considerablement reduit.\n\n" .
            "C'est ce qu'on appelle une attaque par dictionnaire cible."
        );

        // -- Preuves / analyse de metadonnees --
        new File($this->localRoot, 'preuves', 'dir', []);
        new File($this->localRoot, 'preuves/photo-suspecte.jpg', 'txt',
            "[Fichier image - 2.4 Mo]\n" .
            "Pour extraire les metadonnees, utilisez la commande :\n" .
            "  exiftool photo-suspecte.jpg\n\n" .
            "(Dans le cadre de cet exercice, les metadonnees sont ci-dessous)\n\n" .
            "=== METADONNEES EXIF ===\n" .
            "Appareil     : iPhone 14 Pro\n" .
            "Date         : 2025-07-14 18:32:05\n" .
            "GPS Latitude : 43.2965\n" .
            "GPS Longitude: 5.3698\n" .
            "Ville        : Marseille, France\n" .
            "Auteur       : admin_music42"
        );
        new File($this->localRoot, 'preuves/document-suspect.pdf', 'txt',
            "[Fichier PDF - 156 Ko]\n\n" .
            "=== METADONNEES PDF ===\n" .
            "Titre    : Plan de projet personnel\n" .
            "Auteur   : admin\n" .
            "Cree le  : 2025-03-22\n" .
            "Logiciel : LibreOffice 7.5\n" .
            "Mots-cles: roblox, projet, secret"
        );
        new File($this->localRoot, 'preuves/email-intercepte.txt', 'txt',
            "De: admin@protonmail.com\n" .
            "A: contact@darkmarket.onion\n" .
            "Date: 2025-09-15 02:14\n" .
            "Objet: Re: Livraison\n\n" .
            "C'est bon, j'ai change mon mdp sur le serveur.\n" .
            "J'ai utilise le nom de ma ville preferee comme base,\n" .
            "avec le numero de mon departement et un point d'exclamation.\n" .
            "Personne ne devinera."
        );

        // -- Outils --
        new File($this->localRoot, 'outils', 'dir', []);
        new File($this->localRoot, 'outils/aide-metadonnees.txt', 'txt',
            "=== Analyse de metadonnees ===\n\n" .
            "Les metadonnees sont des informations cachees dans les fichiers.\n" .
            "Elles peuvent reveler :\n" .
            "- L'auteur du document\n" .
            "- La date de creation\n" .
            "- La localisation GPS (pour les photos)\n" .
            "- Le logiciel utilise\n\n" .
            "Commande : cat <fichier> pour voir les metadonnees extraites\n" .
            "Les fichiers dans /home/preuves/ contiennent des metadonnees utiles."
        );
        new File($this->localRoot, 'outils/aide-ssh.txt', 'txt',
            "=== Connexion SSH ===\n\n" .
            "SSH (Secure Shell) permet de se connecter a distance\n" .
            "a un autre ordinateur de facon securisee.\n\n" .
            "Syntaxe : ssh <utilisateur>@<adresse_ip>\n" .
            "Exemple : ssh admin@192.168.1.42\n\n" .
            "Le serveur demandera ensuite un mot de passe.\n" .
            "Vous avez 3 tentatives avant d'etre deconnecte."
        );

        // ══════════════════════════════════════════════════════════════
        // FILESYSTEM HACKER (serveur du suspect, accessible via SSH)
        // Contient les enigmes specifiques : fichier cache + chiffre.
        // Structure complete du serveur personnel du suspect.
        // ══════════════════════════════════════════════════════════════

        // -- Images (dossiers photos par saison) --
        new File($this->root, 'images', 'dir', []);
        new File($this->root, 'images/photos-ete-2025', 'dir', []);
        new File($this->root, 'images/photos-printemps-2025', 'dir', []);
        new File($this->root, 'images/photos-hiver-2025', 'dir', []);
        new File($this->root, 'images/photos-automne-2025', 'dir', []);
        new File($this->root, 'images/photos-hiver-2024', 'dir', []);
        new File($this->root, 'images/photos-ete-2024', 'dir', []);
        new File($this->root, 'images/photos-hiver-2022', 'dir', []);

        // -- Videos (dossiers videos par saison) --
        new File($this->root, 'videos', 'dir', []);
        new File($this->root, 'videos/videos-ete-2025', 'dir', []);
        new File($this->root, 'videos/videos-printemps-2025', 'dir', []);
        new File($this->root, 'videos/videos-hiver-2025', 'dir', []);
        new File($this->root, 'videos/videos-automne-2025', 'dir', []);
        new File($this->root, 'videos/videos-hiver-2024', 'dir', []);
        new File($this->root, 'videos/videos-ete-2024', 'dir', []);
        new File($this->root, 'videos/videos-hiver-2022', 'dir', []);

        // -- Documents --
        new File($this->root, 'documents', 'dir', []);

        // Documents professionnels (cours, projets)
        new File($this->root, 'documents/documents-professionnels', 'dir', []);
        new File($this->root, 'documents/documents-professionnels/projet-jeu-roblox', 'dir', []);
        new File($this->root, 'documents/documents-professionnels/cours-python.txt', 'txt',
            "Ceci est un document de cours sur les bases et la syntaxe de Python."
        );
        new File($this->root, 'documents/documents-professionnels/cours-maths.txt', 'txt',
            "Notes de cours avancees en algebre lineaire et calcul differentiel."
        );
        new File($this->root, 'documents/documents-professionnels/cours-sql.txt', 'txt',
            "Introduction et requetes complexes pour la gestion de bases de donnees relationnelles."
        );
        new File($this->root, 'documents/documents-professionnels/cours-javascript.txt', 'txt',
            "Un guide de reference rapide pour les fonctions asynchrones et l'API DOM."
        );
        new File($this->root, 'documents/documents-professionnels/methode-brut-force.txt', 'txt',
            "La methode par brute force est le fait de tester comme un \"bourin\" " .
            "un tres grand nombre de possibilites de caractere en esperant tomber " .
            "sur le bon mot de passe. Cela est donc tres long.\n" .
            "Mais si on test cette methode avec des elements que nous estimons " .
            "suceptible d'etre present dans le mot de passe, le temps necessaire " .
            "pour trouver ce mot de passe s'en trouve donc reduit."
        );

        // Documents personnels
        new File($this->root, 'documents/documents-personnels', 'dir', []);
        new File($this->root, 'documents/documents-personnels/anniversaires.txt', 'txt',
            "Anniversaire maman : 12 Janvier\n" .
            "Anniversaire papa : 25 Mai"
        );
        new File($this->root, 'documents/documents-personnels/la-plus-belle-ville-du-monde.txt', 'txt',
            "O ma Marseille, cite de lumiere, ou le ciel bleu rencontre la mer.\n" .
            "Du Vieux-Port vibrant aux calanques secretes,\n" .
            "nulle part ailleurs je n'ai trouve tant de fetes.\n" .
            "Tes quartiers chantent, ton soleil rechauffe l'ame,\n" .
            "tu es la plus belle ville, mon eternelle flamme."
        );

        // Documents confidentiels (mots de passe + enigmes)
        new File($this->root, 'documents/documents-confidentiels', 'dir', []);
        new File($this->root, 'documents/documents-confidentiels/mot-de-passe-twitter.txt', 'txt',
            "Utilisateur: @fakeAccount_42\nMot de passe: SecureP@sswOrd123"
        );
        new File($this->root, 'documents/documents-confidentiels/mot-de-passe-email.txt', 'txt',
            "Le mot de passe pour l'adresse e-mail personnelle est: EmailS3cret!"
        );

        // -- Enigme 1 : fichier cache (visible uniquement dans le JSON via F12) --
        new File($this->root, 'documents/documents-confidentiels/.plan-secret.txt', 'txt',
            "=== PLAN CONFIDENTIEL ===\n\n" .
            "Phase 1 : Collecter les donnees via le phishing\n" .
            "Phase 2 : Revendre sur le darknet\n" .
            "Phase 3 : Transferer les fonds via crypto\n\n" .
            "Cle de chiffrement pour le fichier protege : CIGALE\n\n" .
            "IMPORTANT : ne jamais laisser ce fichier visible."
        );

        // -- Enigme 2 : document chiffre (Vigenere avec cle "CIGALE") --
        // Texte clair : "Le point de rendez-vous est au Vieux-Port de Marseille.
        //                 La transaction aura lieu le quinze octobre a minuit.
        //                 Apportez les donnees sur une cle USB chiffree."
        // Chiffre avec Vigenere cle CIGALE :
        new File($this->root, 'documents/documents-confidentiels/message-chiffre.txt', 'txt',
            "=== DOCUMENT PROTEGE ===\n" .
            "Ce message est chiffre avec le chiffrement de Vigenere.\n" .
            "Trouvez la cle pour le dechiffrer.\n\n" .
            "Ng roqpv fi tgpfgb-xqxu guv gw Xkixz-Rstr fi Ogtumknni.\n" .
            "Ng vvepugdvqqp cwte nkiy niswkpbg qevsdtg g okpwlv.\n" .
            "Grrsttib niu fsppgiu uwt xpi eng YUD eklhhtgg."
        );

        // -- Telechargements & Musiques --
        new File($this->root, 'telechargement', 'dir', []);
        new File($this->root, 'musiques', 'dir', []);

    }

    /**
     * Racine du filesystem hacker (SSH)
     *
     * @return File
     */
    public function getRoot(): File
    {
        return $this->root;
    }

    /**
     * Racine du filesystem local (guest)
     *
     * @return File
     */
    public function getLocalRoot(): File
    {
        return $this->localRoot;
    }

    /**
     * @return array ['user' => ..., 'host' => ..., 'pass' => ...]
     */
    public function getCredentials(): array
    {
        return $this->sshCredentials;
    }

    /**
     * Trouve un fichier dans le filesystem hacker
     *
     * @param array $path Segments du chemin
     * @return File|null
     */
    public function find(array $path): ?File
    {
        return $this->findIn($this->root, $path);
    }

    /**
     * Trouve un fichier dans le filesystem local
     *
     * @param array $path Segments du chemin
     * @return File|null
     */
    public function findLocal(array $path): ?File
    {
        return $this->findIn($this->localRoot, $path);
    }

    /**
     * Recherche dans une arborescence donnee
     *
     * @param File $root Racine de recherche
     * @param array $path Segments du chemin
     * @return File|null
     */
    private function findIn(File $root, array $path): ?File
    {
        array_shift($path);
        $current = $root;
        foreach ($path as $fileName) {
            $current = $current->getChild($fileName);
        }
        return $current;
    }

}