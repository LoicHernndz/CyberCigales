<?php


namespace helpers;
use Models\Bash\Bash;

class BashRequest
{

    public function control()
    {
        $env = new Bash();

        $input = $_REQUEST["input"] ?? "";
        $path = $_REQUEST["path"] ?? "/home";

        if (empty($input)) {
            echo json_encode(["path" => $path, "output" => ""]);
            return;
        }

        $args = explode(" ", trim($input));
        $command = $args[0];

        $allowed = ["pwd", "ls", "cd", "cat", "help", "sudo_access"];

        if (in_array($command, $allowed)) {
            $result = $this->$command($env, $args, $path);
            echo json_encode($result);
        } else {
            echo json_encode([
                "path" => $path,
                "output" => "bash: " . htmlspecialchars($command) . ": commande introuvable"
            ]);
        }
    }

    private function ls($env, $args, $path)
    {
        $dir = $env->find(explode("/", trim($path, "/")));
        if (!$dir || $dir->getType() !== "dir") {
            return ["path" => $path, "output" => "ls: impossible d'accéder à '$path': Aucun dossier de ce type"];
        }

        $children = $dir->getContent();
        $output = "";
        $hiddenFiles = [];

        foreach ($children as $file) {
            // ÉNIGME: On ne montre pas les fichiers cachés (commençant par .) dans l'UI
            // Mais ils seront présents dans le JSON brut si on regarde l'onglet Network !
            if (strpos($file->getName(), '.') === 0) {
                $hiddenFiles[] = $file->getName();
                continue;
            }

            $colorClass = ($file->getType() === "dir") ? "dir" : "file";
            $output .= "<span class='$colorClass'>" . htmlspecialchars($file->getName()) . "</span>  ";
        }

        return [
            "path" => $path,
            "output" => $output,
            "debug_hint" => !empty($hiddenFiles) ? "Fichiers cachés détectés dans le flux réseau..." : null
        ];
    }

    private function cd($env, $args, $path)
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

        $dir = $env->find($currentPathArray);
        $child = $dir->getChild($target);

        if ($child && $child->getType() === "dir") {
            $newPath = ($path === "/") ? "/$target" : "$path/$target";
            return ["path" => $newPath, "output" => ""];
        }

        return ["path" => $path, "output" => "bash: cd: $target: Aucun dossier de ce type"];
    }

    private function cat($env, $args, $path)
    {
        if (count($args) < 2) return ["path" => $path, "output" => "cat: argument manquant"];

        $target = $args[1];
        $dir = $env->find(explode("/", trim($path, "/")));
        $file = $dir->getChild($target);

        if ($file && $file->getType() !== "dir") {
            return ["path" => $path, "output" => nl2br(htmlspecialchars($file->getContent()))];
        }

        return ["path" => $path, "output" => "cat: $target: Aucun fichier de ce type"];
    }

    private function pwd($env, $args, $path)
    {
        return ["path" => $path, "output" => $path];
    }

    private function help($env, $args, $path)
    {
        $output = "Commandes disponibles :<br>";
        $output .= "ls   - Lister les fichiers<br>";
        $output .= "cd   - Changer de dossier<br>";
        $output .= "cat  - Lire un fichier<br>";
        $output .= "pwd  - Afficher le chemin actuel<br>";
        $output .= "clear - Effacer l'écran";
        return ["path" => $path, "output" => $output];
    }

    private function sudo_access($env, $args, $path)
    {
        return [
            "path" => $path,
            "output" => "<span style='color: #00ff00;'>[SUCCESS] Accès administrateur temporaire accordé.</span><br>Indice : Le secret est caché dans un fichier invisible du dossier /home/documents/documents-confidentiels. Utilisez vos outils d'analyse réseau (F12) lors d'un 'ls' pour le voir."
        ];
    }
}