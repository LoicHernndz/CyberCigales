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
    private array $commandHistory;
    private bool $isTerminalLoggedIn;
    private File $root;
    private array $login;


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

        //  Images
        new File($this->root, 'images', 'dir', []);
        new File($this->root, 'images/photos-ete-2025', 'dir', []);
        new File($this->root, 'images/photos-printemps-2025', 'dir', []);
        new File($this->root, 'images/photos-hiver-2025', 'dir', []);
        new File($this->root, 'images/photos-automne-2025', 'dir', []);
        new File($this->root, 'images/photos-hiver-2024', 'dir', []);
        new File($this->root, 'images/photos-ete-2024', 'dir', []);
        new File($this->root, 'images/photos-hiver-2022', 'dir', []);

        //  Videos
        new File($this->root, 'videos', 'dir', []);
        new File($this->root, 'videos/videos-ete-2025', 'dir', []);
        new File($this->root, 'videos/videos-printemps-2025', 'dir', []);
        new File($this->root, 'videos/videos-hiver-2025', 'dir', []);
        new File($this->root, 'videos/videos-automne-2025', 'dir', []);
        new File($this->root, 'videos/videos-hiver-2024', 'dir', []);
        new File($this->root, 'videos/videos-ete-2024', 'dir', []);
        new File($this->root, 'videos/videos-hiver-2022', 'dir', []);

        //  Documents
        new File($this->root, 'documents', 'dir', []);

        new File($this->root, 'documents/documents-professionnels', 'dir', []);
        new File($this->root, 'documents/documents-professionnels/projet-jeu-roblox', 'dir', []);
        new File(
            $this->root,
            'documents/documents-professionnels/cours-python.txt',
            'txt',
            'Ceci est un document de cours sur les bases et la syntaxe de Python.'
        );
        new File(
            $this->root,
            'documents/documents-professionnels/cours-maths.txt',
            'txt',
            'Notes de cours avancées en algèbre linéaire et calcul différentiel.'
        );
        new File(
            $this->root,
            'documents/documents-professionnels/cours-sql.txt',
            'txt',
            'Introduction et requêtes complexes pour la gestion de bases de données relationnelles.'
        );
        new File(
            $this->root,
            'documents/documents-professionnels/cours-javascript.txt',
            'txt',
            'Un guide de référence rapide pour les fonctions asynchrones et l\'API DOM.'
        );
        new File($this->root, 'documents/documents-professionnels/methode-brut-force.txt', 'txt', 'La méthode par brute force est le fait de tester comme un "bourin" un très grand nombre de possibilités de caractère en espérant tomber sur le bon mot de passe. ' .
            "Cela est donc très long. \nMais si on test cette méthode avec des éléments que nous estimons suceptible d'être présent dans le mot de passe, le temps nécessaire pour trouver ce mot de passe s'en trouve donc réduit.");

        new File($this->root, 'documents/documents-personnels', 'dir', []);
        new File($this->root, 'documents/documents-personnels/anniversaires.txt', 'txt', 'Mère: 14 Avril\nPère: 25 Mai');
        new File(
            $this->root,
            'documents/documents-personnels/la-plus-belle-ville-du-monde.txt',
            'txt',
            "Ô ma Marseille, cité de lumière, où le ciel bleu rencontre la mer.\nDu Vieux-Port vibrant aux calanques secrètes, nulle part ailleurs je n'ai trouvé tant de fêtes.\nTes quartiers chantent, ton soleil réchauffe l'âme, tu es la plus belle ville, mon éternelle flamme."
        );

        new File($this->root, 'documents/documents-confidentiels', 'dir', []);
        new File($this->root, 'documents/documents-confidentiels/mot-de-passe-twitter.txt', 'txt', 'Utilisateur: @fakeAccount_42\nMot de passe: SecureP@sswOrd123');
        new File($this->root, 'documents/documents-confidentiels/mot-de-passe-email.txt', 'txt', 'Le mot de passe pour l\'adresse e-mail personnelle est: EmailS3cret!');

        //  Telechargements & Musiques
        new File($this->root, 'telechargement', 'dir', []);
        new File($this->root, 'musiques', 'dir', []);

    }

    /**
     * Récupère le répertoire racine
     * 
     * @return File Le répertoire racine de l'arborescence
     */
    public function getRoot(): File
    {
        return $this->root;
    }

    /**
     * Trouve un fichier ou dossier par son chemin
     * 
     * @param array $path Tableau des segments du chemin (ex: ['home', 'documents'])
     * @return File|null Le fichier/dossier trouvé ou null
     */
    public function find(array $path): ?File
    {
        array_shift($path);
        $current = $this->root;
        foreach ($path as $fileName) {
            $current = $current->getChild($fileName);
        }
        return $current;
    }

}