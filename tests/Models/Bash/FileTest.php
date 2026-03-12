<?php

namespace Tests\Models\Bash;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Models\Bash\File;
use Exceptions\bash\InvalidPathException;

class FileTest extends TestCase
{
    private File $root;

    protected function setUp(): void
    {
        $this->root = new File(null, 'home', 'dir', []);
    }

    // =============================================
    // Construction et propriétés
    // =============================================

    #[Test]
    public function rootFileHasCorrectProperties(): void
    {
        $this->assertSame('home', $this->root->getName());
        $this->assertSame('dir', $this->root->getType());
        $this->assertSame([], $this->root->getContent());
    }

    #[Test]
    public function childFileIsAutoAddedToParent(): void
    {
        $child = new File($this->root, 'documents', 'dir', []);
        $this->assertCount(1, $this->root->getContent());
        $this->assertSame($child, $this->root->getContent()[0]);
    }

    #[Test]
    public function textFileHasStringContent(): void
    {
        new File($this->root, 'docs', 'dir', []);
        $file = new File($this->root, 'docs/hello.txt', 'txt', 'Hello World');
        $this->assertSame('hello.txt', $file->getName());
        $this->assertSame('txt', $file->getType());
        $this->assertSame('Hello World', $file->getContent());
    }

    #[Test]
    public function nestedPathCreatesCorrectHierarchy(): void
    {
        new File($this->root, 'a', 'dir', []);
        new File($this->root, 'a/b', 'dir', []);
        $deep = new File($this->root, 'a/b/c.txt', 'txt', 'content');

        $this->assertSame('c.txt', $deep->getName());
        $this->assertSame('b', $deep->getPrev()->getName());
    }

    // =============================================
    // getPath()
    // =============================================

    #[Test]
    public function rootPathIsEmpty(): void
    {
        $this->assertSame('', $this->root->getPath());
    }

    #[Test]
    public function childPathIncludesParent(): void
    {
        $child = new File($this->root, 'documents', 'dir', []);
        $this->assertSame('/documents', $child->getPath());
    }

    #[Test]
    public function deepPathBuildsCorrectly(): void
    {
        new File($this->root, 'a', 'dir', []);
        new File($this->root, 'a/b', 'dir', []);
        $file = new File($this->root, 'a/b/test.txt', 'txt', '');
        $this->assertSame('/a/b/test.txt', $file->getPath());
    }

    // =============================================
    // getChild()
    // =============================================

    #[Test]
    public function getChildFindsExistingChild(): void
    {
        $child = new File($this->root, 'docs', 'dir', []);
        $this->assertSame($child, $this->root->getChild('docs'));
    }

    #[Test]
    public function getChildReturnsNullForMissing(): void
    {
        $this->assertNull($this->root->getChild('nonexistent'));
    }

    #[Test]
    public function getChildReturnsNullOnFile(): void
    {
        new File($this->root, 'docs', 'dir', []);
        $file = new File($this->root, 'docs/readme.txt', 'txt', '');
        $this->assertNull($file->getChild('anything'));
    }

    // =============================================
    // addFile() — exception sur non-dossier
    // =============================================

    #[Test]
    public function addFileOnNonDirThrowsException(): void
    {
        new File($this->root, 'docs', 'dir', []);
        $file = new File($this->root, 'docs/readme.txt', 'txt', 'content');

        $this->expectException(InvalidPathException::class);
        $child = new File(null, 'orphan', 'txt', '');
        $file->addFile($child);
    }

    // =============================================
    // relativeResolution()
    // =============================================

    #[Test]
    public function relativeResolutionWithDotDot(): void
    {
        $docs = new File($this->root, 'docs', 'dir', []);
        $this->assertSame('', $docs->relativeResolution(['..']));
    }

    #[Test]
    public function relativeResolutionWithChildName(): void
    {
        $docs = new File($this->root, 'docs', 'dir', []);
        new File($this->root, 'docs/file.txt', 'txt', '');
        $this->assertSame('/docs/file.txt', $docs->relativeResolution(['file.txt']));
    }

    #[Test]
    public function relativeResolutionCombined(): void
    {
        new File($this->root, 'a', 'dir', []);
        $b = new File($this->root, 'a/b', 'dir', []);
        new File($this->root, 'a/c', 'dir', []);

        // Depuis b, remonter (..) puis aller dans c
        $this->assertSame('/a/c', $b->relativeResolution(['..', 'c']));
    }

    // =============================================
    // Setters
    // =============================================

    #[Test]
    public function settersUpdateProperties(): void
    {
        $file = new File($this->root, 'old', 'dir', []);
        $file->setName('new');
        $file->setType('txt');
        $file->setContent('data');

        $this->assertSame('new', $file->getName());
        $this->assertSame('txt', $file->getType());
        $this->assertSame('data', $file->getContent());
    }

    #[Test]
    public function setPrevChangesParent(): void
    {
        $dir1 = new File($this->root, 'dir1', 'dir', []);
        $dir2 = new File($this->root, 'dir2', 'dir', []);
        $file = new File($this->root, 'dir1/file.txt', 'txt', '');

        $file->setPrev($dir2);
        $this->assertSame($dir2, $file->getPrev());
    }
}
