<?php

namespace Models\Bash;

use Exception;
use Exceptions\bash\CommandCallingException;
use Exceptions\bash\InvalidPathException;

/**
 * Modèle de fichier/dossier pour l'environnement Bash simulé
 * 
 * Représente un fichier ou dossier dans l'arborescence virtuelle.
 */
class File
{
    /** @var File|null Répertoire parent (null pour la racine) */
    private File|null $prev;

    /** @var string Nom du fichier ou dossier */
    private string $name;

    /** @var string Type : 'dir' pour dossier, 'txt' ou autre pour fichier */
    private string $type;

    /** @var mixed Contenu : array<File> pour un dossier, string pour un fichier */
    private mixed $content;

    /**
     * @param File|null $root
     * @param String $path
     * @param String $type
     * @param mixed $content
     * @throws Exception
     */
    public function __construct(File|null $root, string $path, string $type, mixed $content)
    {
        $pathArray = explode("/", $path);

        $this->prev = $root;
        for ($i = 0; $i < sizeof($pathArray) - 1; ++$i){
            foreach ($this->prev->getContent() as $file) {
                if ($file->getType() == "dir" && $file->getName() == $pathArray[$i]) {
                    $this->prev = $file;
                    break;
                }
            }
            if ($this->prev->getName() != $pathArray[$i]) { throw new InvalidPathException("Bad path : ".$path); }
        }
        $this->type = $type;
        $this->content = $content;
        $this->name = $pathArray[sizeof($pathArray) - 1];

        $this->prev?->addFile($this);       //  Si prev non null, ajouter au dossier
    }

    /** @return File Répertoire parent */
    public function getPrev(): File { return $this->prev; }

    /** @param File $prev Nouveau répertoire parent */
    public function setPrev(File $prev): void { $this->prev = $prev; }

    /** @return string Nom du fichier/dossier */
    public function getName(): string { return $this->name; }

    /** @param string $name Nouveau nom */
    public function setName(string $name): void { $this->name = $name; }

    /** @return string Type du fichier ('dir', 'txt', etc.) */
    public function getType(): string { return $this->type; }

    /** @param string $type Nouveau type */
    public function setType(string $type): void { $this->type = $type; }

    /** @return mixed Contenu (array<File> pour dossier, string pour fichier) */
    public function getContent(): mixed { return $this->content; }

    /** @param mixed $content Nouveau contenu */
    public function setContent(mixed $content): void { $this->content = $content; }

    /**
     * Construit le chemin absolu du fichier en remontant l'arborescence
     *
     * @return string Chemin absolu (ex: '/home/documents/fichier.txt')
     */
    public function getPath(): string {
        if ($this->prev == null) { return ""; }
        return $this->prev->getPath()."/".$this->getName();
    }

    /**
     * Ajoute un fichier enfant à ce dossier
     *
     * @param File $file Fichier à ajouter
     * @return void
     * @throws InvalidPathException Si ce n'est pas un dossier
     */
    public function addFile(File $file): void {
        if ($this->getType() != "dir") {
            throw new InvalidPathException($this->getName()." n'est pas un dossier.");
        }
        $this->content[] = $file;
    }

    /**
     * Recherche un enfant direct par son nom
     *
     * @param string $name Nom du fichier/dossier recherché
     * @return File|null L'enfant trouvé ou null
     */
    public function getChild(string $name): File|null {
        if ($this->getType() != "dir") { return null; }
        foreach ($this->content as $file) {
            if ($file->getName() == $name) { return $file; }
        }
        return null;
    }

    /**
     * Résout un chemin relatif depuis ce fichier/dossier
     *
     * @param array<int, string> $path Segments du chemin relatif ('..' pour remonter)
     * @return string Chemin absolu résolu
     */
    public function relativeResolution(array $path): string {
        $current = $this;
        foreach ($path as $fileName) {
            if ($fileName == "..") { $current = $current->getPrev(); }
            else { $current = $current->getChild($fileName); }
        }
        return $current->getPath();
    }
}