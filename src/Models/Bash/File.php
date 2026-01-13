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
    private File|null $prev;
    private String $name;
    private String $type;
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

    public function getPrev(): File { return $this->prev; }
    public function setPrev(File $prev): void { $this->prev = $prev; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }
    public function getContent(): mixed { return $this->content; }
    public function setContent(mixed $content): void { $this->content = $content; }

    public function getPath(): string {
        if ($this->prev == null) { return ""; }
        return $this->prev->getPath()."/".$this->getName();
    }

    /**
     * @throws Exception
     */
    public function addFile(File $file): void {
        if ($this->getType() != "dir") {
            throw new InvalidPathException($this->getName()." n'est pas un dossier.");
        }
        $this->content[] = $file;
    }

    public function getChild(string $name): File|null {
        if ($this->getType() != "dir") { return null; }
        foreach ($this->content as $file) {
            if ($file->getName() == $name) { return $file; }
        }
        return null;
    }

    public function relativeResolution(array $path): string {
        $current = $this;
        foreach ($path as $fileName) {
            if ($fileName == "..") { $current = $current->getPrev(); }
            else { $current = $current->getChild($fileName); }
        }
        return $current->getPath();
    }
}