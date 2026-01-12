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
        $this->login = ['user' => 'admin', 'pass' =>'secure'];

        //  Images
        New File($this->root, 'images', 'dir', []);
        New File($this->root, 'images/photos-ete-2025', 'dir', []);
        New File($this->root, 'images/photos-printemps-2025', 'dir', []);
        New File($this->root, 'images/photos-hiver-2025', 'dir', []);
        New File($this->root, 'images/photos-automne-2025', 'dir', []);
        New File($this->root, 'images/photos-hiver-2024', 'dir', []);
        New File($this->root, 'images/photos-ete-2024', 'dir', []);
        New File($this->root, 'images/photos-hiver-2022', 'dir', []);

        //  Videos
        New File($this->root, 'videos', 'dir', []);
        New File($this->root, 'videos/videos-ete-2025', 'dir', []);
        New File($this->root, 'videos/videos-printemps-2025', 'dir', []);
        New File($this->root, 'videos/videos-hiver-2025', 'dir', []);
        New File($this->root, 'videos/videos-automne-2025', 'dir', []);
        New File($this->root, 'videos/videos-hiver-2024', 'dir', []);
        New File($this->root, 'videos/videos-ete-2024', 'dir', []);
        New File($this->root, 'videos/videos-hiver-2022', 'dir', []);

        //  Documents
        New File($this->root, 'documents', 'dir', []);

        New File($this->root, 'documents/documents-professionnels', 'dir', []);
        New File($this->root, 'documents/documents-professionnels/projet-jeu-roblox', 'dir', []);
        New File($this->root, 'documents/documents-professionnels/cours-python.txt', 'txt',
            'Ceci est un document de cours sur les bases et la syntaxe de Python.');
        New File($this->root, 'documents/documents-professionnels/cours-maths.txt', 'txt',
            'Notes de cours avancées en algèbre linéaire et calcul différentiel.');
        New File($this->root, 'documents/documents-professionnels/cours-sql.txt', 'txt',
            'Introduction et requêtes complexes pour la gestion de bases de données relationnelles.');
        New File($this->root, 'documents/documents-professionnels/cours-javascript.txt', 'txt',
            'Un guide de référence rapide pour les fonctions asynchrones et l\'API DOM.');
        New File($this->root, 'documents/documents-professionnels/methode-brut-force.txt', 'txt', 'La méthode par brute force est le fait de tester comme un "bourin" un très grand nombre de possibilités de caractère en espérant tomber sur le bon mot de passe. ' .
            "Cela est donc très long. \nMais si on test cette méthode avec des éléments que nous estimons suceptible d'être présent dans le mot de passe, le temps nécessaire pour trouver ce mot de passe s'en trouve donc réduit.");

        New File($this->root, 'documents/documents-personnels', 'dir', []);
        New File($this->root, 'documents/documents-personnels/anniversaires.txt', 'txt', 'Mère: 12 Janvier\nPère: 25 Mai');
        New File($this->root, 'documents/documents-personnels/la-plus-belle-ville-du-monde.txt', 'txt',
            "Ô ma Marseille, cité de lumière, où le ciel bleu rencontre la mer.\nDu Vieux-Port vibrant aux calanques secrètes, nulle part ailleurs je n'ai trouvé tant de fêtes.\nTes quartiers chantent, ton soleil réchauffe l'âme, tu es la plus belle ville, mon éternelle flamme.");

        New File($this->root, 'documents/documents-confidentiels', 'dir', []);
        New File($this->root, 'documents/documents-confidentiels/mot-de-passe-twitter.txt', 'txt', 'Utilisateur: @fakeAccount_42\nMot de passe: SecureP@sswOrd123');
        New File($this->root, 'documents/documents-confidentiels/mot-de-passe-email.txt', 'txt', 'Le mot de passe pour l\'adresse e-mail personnelle est: EmailS3cret!');

        //  Telechargements & Musiques
        New File($this->root, 'telechargement', 'dir', []);
        New File($this->root, 'musiques', 'dir', []);

    }

    public function getRoot() { return $this->root; }

    public function find(array $path)
    {
        array_shift($path);
        $current = $this->root;
        foreach ($path as $fileName) {
            $current = $current->getChild($fileName);
        }
        return $current;
    }

}