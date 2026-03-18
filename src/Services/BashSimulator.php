<?php


namespace Services;
use config\Database;
use Models\Bash\Bash;
use Models\Bash\File;

/**
 * Simulateur de terminal Bash pour l'escape game
 *
 * Traite les requêtes AJAX pour exécuter des commandes bash (ls, pwd, cd, cat, clear, help) dans l'environnement simulé.
 */
class BashSimulator
{

    /**
     * Point d'entrée pour traiter les requêtes de commandes bash
     * 
     * Récupère l'input, le chemin et exécute la commande autorisée.
     * 
     * @return void
     */
    public function control()
    {
        // Démarrer la session si ce n'est pas déjà fait pour l'historique
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $env = new Bash();
        $this->maybeUnlockCampusJpg($env);

        // Action SSH login (verification des identifiants)
        $action = $_REQUEST["action"] ?? "";
        if ($action === "ssh_login") {
            $this->sshLogin($env);
            return;
        }

        $input = $_REQUEST["input"] ?? "";
        $path = $_REQUEST["path"] ?? "/home";
        $mode = $_REQUEST["mode"] ?? "ssh";

        if (empty($input)) {
            echo json_encode(["path" => $path, "output" => ""]);
            return;
        }

        // Ajouter la commande à l'historique
        if (!isset($_SESSION['bash_history'])) {
            $_SESSION['bash_history'] = [];
        }
        $_SESSION['bash_history'][] = $input;

        $args = explode(" ", trim($input));
        $command = $args[0];

        $allowed = ["pwd", "ls", "cd", "cat", "clear", "help", "sudo_access", "whoami", "date", "echo", "history", "man", "grep", "touch", "mkdir", "rm", "ping", "ssh"];

        if (in_array($command, $allowed)) {
            $result = $this->$command($env, $args, $path, $mode);
            echo json_encode($result);
        } else {
            echo json_encode([
                "path" => $path,
                "output" => "bash: " . htmlspecialchars($command) . ": commande introuvable"
            ]);
        }
    }

    /**
     * Débloque dynamiquement des fichiers locaux en fonction
     * de la progression du chat Instagram (persistée en base).
     */
    private function maybeUnlockCampusJpg(Bash $env): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return;
        }

        $progress = $this->getChatProgress((int)$userId, 'avsl_ydbjb');
        if ($progress < 2) {
            return;
        }

        // Créer /home/téléchargements/campus.jpg si absent
        if ($env->findLocal(['home', 'téléchargements']) === null) {
            new File($env->getLocalRoot(), 'téléchargements', 'dir', []);
        }
        if ($env->findLocal(['home', 'téléchargements', 'campus.jpg']) === null) {
            new File(
                $env->getLocalRoot(),
                'téléchargements/campus.jpg',
                'txt',
                "[Fichier image - campus.jpg]\n" .
                "=== METADONNEES (EXIF) ===\n" .
                "Date/Time Original: 2026:03:01 18:21:09\n" .
                "GPS Latitude      : 43,22980° N\n" .
                "GPS Longitude     : 5,44292° E\n" .
                "Comment           : Cette fois je ne pourrais pas le perdre. Je pense que ça sera assez précis, avec un peu d'aide de Google Maps et un peu d'observation, ça devrait prendre beaucoup moins de temps. Ah, et la combinaison aussi...\n"
            );
        }
    }

    private function getChatProgress(int $userId, string $chatName): int
    {
        $db = new Database();
        $db->query('SELECT progress_index FROM user_chat_progress WHERE user_id = :user_id AND chat_name = :chat_name');
        $db->bind(':user_id', $userId);
        $db->bind(':chat_name', $chatName);
        $result = $db->single();

        return $result ? (int)$result->progress_index : 0;
    }

    /**
     * Resout un chemin dans le bon filesystem selon le mode
     *
     * @param Bash $env L'environnement bash
     * @param array $pathArray Segments du chemin
     * @param string $mode 'local' ou 'ssh'
     * @return \Models\Bash\File|null
     */
    private function resolve(Bash $env, array $pathArray, string $mode)
    {
        if ($mode === "local") {
            return $env->findLocal($pathArray);
        }
        return $env->find($pathArray);
    }

    /**
     * Exécute la commande ls (liste le contenu d'un répertoire)
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array Le résultat de la commande avec path et output
     */
    private function ls($env, $args, $path, $mode = "ssh")
    {
        $dir = $this->resolve($env, explode("/", trim($path, "/")), $mode);
        if (!$dir || $dir->getType() !== "dir") {
            return ["path" => $path, "output" => "ls: impossible d'accéder à '$path': Aucun dossier de ce type"];
        }

        $children = $dir->getContent();
        $output = "";
        $hiddenFiles = [];

        foreach ($children as $file) {
            // ÉNIGME: On ne montre pas les fichiers cachés (commençant par .) dans l'UI
            // Mais ils seront présents dans le JSON brut si on regarde l'onglet Network !
            if (str_starts_with($file->getName(), '.')) {
                $hiddenFiles[] = $file->getName();
                continue;
            }

            $colorClass = ($file->getType() === "dir") ? "dir" : "file";
            $output .= "<span class='$colorClass'>" . htmlspecialchars($file->getName()) .
                str_repeat("&nbsp", ((strlen(htmlspecialchars($file->getName())) % 4==0) ? 4 : strlen(htmlspecialchars($file->getName())) % 4))
                 . "</span>  ";

        }

        return [
            "path" => $path,
            "output" => $output,
            "debug_hint" => !empty($hiddenFiles) ? "Fichiers cachés détectés dans le flux réseau..." : null
        ];
    }

    /**
     * Exécute la commande cd (change de répertoire)
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array Le nouveau chemin après changement de répertoire
     */
    private function cd($env, $args, $path, $mode = "ssh")
    {
        if (count($args) < 2) return ["path" => $path, "output" => ""];

        $target = $args[1];
        $currentPathArray = explode("/", trim($path, "/"));

        if ($target === "..") {
            if (count($currentPathArray) > 1) {
                array_pop($currentPathArray);
            }
            $newPath = "/" . implode("/", $currentPathArray);
            return ["path" => $newPath, "output" => ""];
        }

        $dir = $this->resolve($env, $currentPathArray, $mode);
        $child = $dir->getChild($target);

        if ($child && $child->getType() === "dir") {
            $newPath = ($path === "/") ? "/$target" : "$path/$target";
            return ["path" => $newPath, "output" => ""];
        }

        return ["path" => $path, "output" => "bash: cd: $target: Aucun dossier de ce type"];
    }

    /**
     * Exécute la commande cat (affiche le contenu d'un fichier)
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array Le contenu du fichier
     */
    private function cat($env, $args, $path, $mode = "ssh")
    {
        if (count($args) < 2) return ["path" => $path, "output" => "cat: argument manquant"];

        $target = $args[1];
        $dir = $this->resolve($env, explode("/", trim($path, "/")), $mode);
        $file = $dir->getChild($target);

        if ($file && $file->getType() !== "dir") {
            return ["path" => $path, "output" => nl2br(htmlspecialchars($file->getContent()))];
        }

        return ["path" => $path, "output" => "cat: $target: Aucun fichier de ce type"];
    }

    /**
     * Exécute la commande pwd (affiche le répertoire de travail actuel)
     * 
     * Retourne le chemin actuel.
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array Le chemin actuel
     */
    private function pwd($env, $args, $path, $mode = "ssh")
    {
        return ["path" => $path, "output" => $path];
    }

    /**
     * Exécute la commande clear (efface l'écran du terminal)
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array Signal pour effacer l'écran
     */
    private function clear($env, $args, $path, $mode = "ssh") {
        return ["path" => $path, "output" => "@CLEAR"];
    }

    /**
     * Exécute la commande help (affiche l'aide des commandes disponibles)
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array La liste des commandes disponibles
     */
    private function help($env, $args, $path, $mode = "ssh")
    {
        $output = "Commandes disponibles :<br>";
        $output .= "ls   - Lister les fichiers<br>";
        $output .= "cd   - Changer de dossier<br>";
        $output .= "cat  - Lire un fichier<br>";
        $output .= "pwd  - Afficher le chemin actuel<br>";
        $output .= "clear - Effacer l'écran<br>";
        $output .= "whoami - Afficher l'utilisateur actuel<br>";
        $output .= "date - Afficher la date<br>";
        $output .= "echo - Afficher du texte<br>";
        $output .= "history - Afficher l'historique<br>";
        $output .= "man - Afficher le manuel d'une commande<br>";
        $output .= "grep - Rechercher dans un fichier<br>";
        return ["path" => $path, "output" => $output];
    }

    /**
     * Commande spéciale sudo_access (donne un indice pour trouver les fichiers cachés)
     * 
     * @param Bash $env L'environnement bash
     * @param array $args Les arguments de la commande
     * @param string $path Le chemin actuel
     * @return array Message d'accès administrateur avec indice
     */
    private function sudo_access($env, $args, $path, $mode = "ssh")
    {
        return [
            "path" => $path,
            "output" => "<span style='color: #00ff00;'>[SUCCESS] Accès administrateur temporaire accordé.</span><br>Indice : Le secret est caché dans un fichier invisible du dossier /home/documents/documents-confidentiels. Utilisez vos outils d'analyse réseau (F12) lors d'un 'ls' pour le voir."
        ];
    }

    /**
     * Vérifie les identifiants SSH
     *
     * Compare l'utilisateur, l'hôte et le mot de passe envoyés
     * avec les identifiants attendus définis dans le modèle Bash.
     *
     * @param Bash $env L'environnement bash contenant les identifiants
     * @return void
     */
    private function sshLogin(Bash $env): void
    {
        $user = $_REQUEST["user"] ?? "";
        $host = $_REQUEST["host"] ?? "";
        $pass = $_REQUEST["pass"] ?? "";

        $credentials = $env->getCredentials();

        $authenticated = (
            $user === $credentials['user'] &&
            $host === $credentials['host'] &&
            $pass === $credentials['pass']
        );

        echo json_encode(["authenticated" => $authenticated]);
    }

    private function whoami($env, $args, $path)
    {
        return ["path" => $path, "output" => "guest"];
    }

    private function date($env, $args, $path)
    {
        return ["path" => $path, "output" => date('D M d H:i:s T Y')];
    }

    private function echo($env, $args, $path)
    {
        array_shift($args);
        return ["path" => $path, "output" => htmlspecialchars(implode(" ", $args))];
    }

    private function history($env, $args, $path)
    {
        $history = $_SESSION['bash_history'] ?? [];
        $output = "";
        foreach ($history as $i => $cmd) {
            $output .= ($i + 1) . "  " . htmlspecialchars($cmd) . "<br>";
        }
        return ["path" => $path, "output" => $output];
    }

    private function man($env, $args, $path)
    {
        if (count($args) < 2) {
            return ["path" => $path, "output" => "Quel manuel voulez-vous ? (ex: man ls)"];
        }

        $cmd = $args[1];
        $manuals = [
            "ls" => "ls - list directory contents",
            "cd" => "cd - change the shell working directory",
            "cat" => "cat - concatenate files and print on the standard output",
            "pwd" => "pwd - print name of current/working directory",
            "clear" => "clear - clear the terminal screen",
            "whoami" => "whoami - print effective userid",
            "date" => "date - print or set the system date and time",
            "echo" => "echo - display a line of text",
            "history" => "history - display the history list",
            "grep" => "grep - print lines matching a pattern",
            "touch" => "touch - change file timestamps (or create empty file)",
            "mkdir" => "mkdir - make directories",
            "rm" => "rm - remove files or directories",
            "ping" => "ping - send ICMP ECHO_REQUEST to network hosts",
            "ssh" => "ssh - OpenSSH SSH client (remote login program)"
        ];

        if (isset($manuals[$cmd])) {
            return ["path" => $path, "output" => $manuals[$cmd]];
        }

        return ["path" => $path, "output" => "Aucune entrée de manuel pour $cmd"];
    }

    private function grep($env, $args, $path)
    {
        if (count($args) < 3) {
            return ["path" => $path, "output" => "usage: grep PATTERN FILE"];
        }

        $pattern = $args[1];
        $filename = $args[2];

        // Nettoyer les guillemets autour du pattern si présents
        $pattern = trim($pattern, "\"'");

        $dir = $env->find(explode("/", trim($path, "/")));
        $file = $dir->getChild($filename);

        if (!$file || $file->getType() === "dir") {
            return ["path" => $path, "output" => "grep: $filename: Aucun fichier de ce type"];
        }

        $content = $file->getContent();
        $lines = explode("\n", $content);

        $output = "";
        foreach ($lines as $line) {
            if (strpos($line, $pattern) !== false) {
                $highlighted = str_replace($pattern, "<span style='color:red'>$pattern</span>", htmlspecialchars($line));
                $output .= $highlighted . "<br>";
            }
        }

        return ["path" => $path, "output" => $output];
    }

    private function touch($env, $args, $path)
    {
        return ["path" => $path, "output" => "touch: permission denied (read-only system)"];
    }

    private function mkdir($env, $args, $path)
    {
        return ["path" => $path, "output" => "mkdir: permission denied (read-only system)"];
    }

    private function rm($env, $args, $path)
    {
        return ["path" => $path, "output" => "rm: permission denied (read-only system)"];
    }

    private function ping($env, $args, $path)
    {
        if (count($args) < 2) {
            return ["path" => $path, "output" => "ping: usage error: Destination address required"];
        }
        $host = htmlspecialchars($args[1]);
        $output = "PING $host (64 bytes data)<br>";
        $output .= "64 bytes from $host: icmp_seq=1 ttl=54 time=12.3 ms<br>";
        $output .= "64 bytes from $host: icmp_seq=2 ttl=54 time=12.5 ms<br>";
        $output .= "64 bytes from $host: icmp_seq=3 ttl=54 time=12.4 ms<br>";
        return ["path" => $path, "output" => $output];
    }

    private function ssh($env, $args, $path)
    {
        if (count($args) < 2) {
            return ["path" => $path, "output" => "usage: ssh user@hostname"];
        }
        $target = htmlspecialchars($args[1]);
        return ["path" => $path, "output" => "ssh: connect to host $target port 22: Connection refused"];
    }
}
