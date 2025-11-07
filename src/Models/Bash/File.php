<?php

namespace Models\Bash;

use Exception;

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
            if ($this->prev != $pathArray[$i]) { throw new Exception("Bad path : ".$path); }
        }
        $this->type = $type;
        $this->content = $content;
        $this->name = $pathArray[sizeof($pathArray) - 1];

        $this->prev->addFile($this);
    }

    public function getPrev(): File { return $this->prev; }
    public function setPrev(File $prev): void { $this->prev = $prev; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }
    public function getContent(): mixed { return $this->content; }
    public function setContent(mixed $content): void { $this->content = $content; }

    /**
     * @throws Exception
     */
    public function addFile(File $file): void {
        if ($file->getType() != "dir") {
            throw new Exception($this->getName()." n'est pas un dossier.");
        }
        $this->content[] = $file;
    }
}