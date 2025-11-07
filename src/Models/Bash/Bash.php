<?php

namespace Models\Bash;

use Exception;

class Bash
{
    private array $commandHistory;
    public bool $isTerminalLoggedIn;
    private File $root;
    private array $login;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $commandHistory = [];
        $isTerminalLoggedIn = false;
        $root = new File(null, 'home', 'dir', []);
        $login = ['user' => 'admin', 'pass' =>'secure'];

        //  Images
        New File($root, 'home/images', 'dir', []);
        New File($root, 'home/images/photos-ete-2025', 'dir', []);
        New File($root, 'home/images/photos-printemps-2025', 'dir', []);
        New File($root, 'home/images/photos-hiver-2025', 'dir', []);
        New File($root, 'home/images/photos-automne-2025', 'dir', []);
        New File($root, 'home/images/photos-hiver-2024', 'dir', []);
        New File($root, 'home/images/photos-ete-2024', 'dir', []);
        New File($root, 'home/images/photos-hiver-2022', 'dir', []);

        //  Videos
        New File($root, 'home/videos', 'dir', []);
        New File($root, 'home/videos/videos-ete-2025', 'dir', []);
        New File($root, 'home/videos/videos-printemps-2025', 'dir', []);
        New File($root, 'home/videos/videos-hiver-2025', 'dir', []);
        New File($root, 'home/videos/videos-automne-2025', 'dir', []);
        New File($root, 'home/videos/videos-hiver-2024', 'dir', []);
        New File($root, 'home/videos/videos-ete-2024', 'dir', []);
        New File($root, 'home/videos/videos-hiver-2022', 'dir', []);

        //  Documents
        New File($root, 'home/documents', 'dir', []);

        New File($root, 'home/documents/documents-professionnels', 'dir', []);
        New File($root, 'home/documents/documents-professionnels/projet-jeu-roblox', 'dir', []);
        New File($root, 'home/documents/documents-professionnels/cours-python.txt', 'txt',
            'Ceci est un document de cours sur les bases et la syntaxe de Python.');
        New File($root, 'home/documents/documents-professionnels/cours-maths.txt', 'txt',
            'Notes de cours avancées en algèbre linéaire et calcul différentiel.');
        New File($root, 'home/documents/documents-professionnels/cours-sql.txt', 'txt',
            'Introduction et requêtes complexes pour la gestion de bases de données relationnelles.');
        New File($root, 'home/documents/documents-professionnels/cours-javascript.txt', 'txt',
            'Un guide de référence rapide pour les fonctions asynchrones et l\'API DOM.');
        New File($root, 'home/documents/documents-professionnels/methode-brut-force.txt', 'txt', 'La méthode par brute force est le fait de tester comme un "bourin" un très grand nombre de possibilités de caractère en espérant tomber sur le bon mot de passe. ' .
            "Cela est donc très long. \nMais si on test cette méthode avec des éléments que nous estimons suceptible d'être présent dans le mot de passe, le temps nécessaire pour trouver ce mot de passe s'en trouve donc réduit.");

        New File($root, 'home/documents/documents-personnels', 'dir', []);
        New File($root, 'home/documents/documents-personnels/anniversaires.txt', 'txt', 'Mère: 12 Janvier\nPère: 25 Mai');
        New File($root, 'home/documents/documents-personnels/la-plus-belle-ville-du-monde.txt', 'txt',
            "Ô ma Marseille, cité de lumière, où le ciel bleu rencontre la mer.\nDu Vieux-Port vibrant aux calanques secrètes, nulle part ailleurs je n'ai trouvé tant de fêtes.\nTes quartiers chantent, ton soleil réchauffe l'âme, tu es la plus belle ville, mon éternelle flamme.");

        New File($root, 'home/documents/documents-confidentiels', 'dir', []);
        New File($root, 'home/documents/documents-confidentiels/mot-de-passe-twitter.txt', 'txt', 'Utilisateur: @fakeAccount_42\nMot de passe: SecureP@sswOrd123');
        New File($root, 'home/documents/documents-confidentiels/mot-de-passe-email.txt', 'txt', 'Le mot de passe pour l\'adresse e-mail personnelle est: EmailS3cret!');

        //  Telechargements & Musiques
        New File($root, 'home/telechargement', 'dir', []);
        New File($root, 'home/musiques', 'dir', []);

    }

}