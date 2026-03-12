<?php

namespace Tests\Models\Bash;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Models\Bash\Bash;
use Models\Bash\File;

class BashTest extends TestCase
{
    private Bash $bash;

    protected function setUp(): void
    {
        $this->bash = new Bash();
    }

    // =============================================
    // Structure des filesystems
    // =============================================

    #[Test]
    public function rootIsDirectory(): void
    {
        $this->assertSame('dir', $this->bash->getRoot()->getType());
        $this->assertSame('home', $this->bash->getRoot()->getName());
    }

    #[Test]
    public function localRootIsDirectory(): void
    {
        $this->assertSame('dir', $this->bash->getLocalRoot()->getType());
        $this->assertSame('home', $this->bash->getLocalRoot()->getName());
    }

    // =============================================
    // Filesystem local (guest)
    // =============================================

    #[Test]
    public function localRootContainsMissionDirectory(): void
    {
        $mission = $this->bash->getLocalRoot()->getChild('mission');
        $this->assertNotNull($mission);
        $this->assertSame('dir', $mission->getType());
    }

    #[Test]
    public function localRootContainsDocumentsDirectory(): void
    {
        $docs = $this->bash->getLocalRoot()->getChild('documents');
        $this->assertNotNull($docs);
        $this->assertSame('dir', $docs->getType());
    }

    #[Test]
    public function localRootContainsPreuvesDirectory(): void
    {
        $preuves = $this->bash->getLocalRoot()->getChild('preuves');
        $this->assertNotNull($preuves);
        $this->assertSame('dir', $preuves->getType());
    }

    #[Test]
    public function localBriefingFileExists(): void
    {
        $result = $this->bash->findLocal(['home', 'mission', 'briefing.txt']);
        $this->assertNotNull($result);
        $this->assertSame('txt', $result->getType());
        $this->assertStringContainsString('BRIEFING', $result->getContent());
    }

    // =============================================
    // Filesystem hacker (SSH)
    // =============================================

    #[Test]
    public function hackerRootContainsImages(): void
    {
        $images = $this->bash->getRoot()->getChild('images');
        $this->assertNotNull($images);
        $this->assertSame('dir', $images->getType());
    }

    #[Test]
    public function hackerRootContainsDocuments(): void
    {
        $docs = $this->bash->getRoot()->getChild('documents');
        $this->assertNotNull($docs);
    }

    #[Test]
    public function hackerSecretFileExists(): void
    {
        $result = $this->bash->find(['home', 'documents', 'documents-confidentiels', '.plan-secret.txt']);
        $this->assertNotNull($result);
        $this->assertStringContainsString('CIGALE', $result->getContent());
    }

    #[Test]
    public function hackerEncryptedMessageExists(): void
    {
        $result = $this->bash->find(['home', 'documents', 'documents-confidentiels', 'message-chiffre.txt']);
        $this->assertNotNull($result);
        $this->assertStringContainsString('Vigenere', $result->getContent());
    }

    // =============================================
    // SSH Credentials
    // =============================================

    #[Test]
    public function credentialsContainRequiredKeys(): void
    {
        $creds = $this->bash->getCredentials();
        $this->assertArrayHasKey('user', $creds);
        $this->assertArrayHasKey('host', $creds);
        $this->assertArrayHasKey('pass', $creds);
    }

    #[Test]
    public function credentialsHaveExpectedValues(): void
    {
        $creds = $this->bash->getCredentials();
        $this->assertSame('admin', $creds['user']);
        $this->assertSame('192.168.1.42', $creds['host']);
        $this->assertSame('Marseille13!', $creds['pass']);
    }

    // =============================================
    // find() et findLocal()
    // =============================================

    #[Test]
    public function findReturnsNullForNonExistentPath(): void
    {
        $result = $this->bash->find(['home', 'nonexistent']);
        $this->assertNull($result);
    }

    #[Test]
    public function findLocalReturnsNullForNonExistentPath(): void
    {
        $result = $this->bash->findLocal(['home', 'nonexistent']);
        $this->assertNull($result);
    }

    #[Test]
    public function findLocatesNestedFile(): void
    {
        $result = $this->bash->find(['home', 'documents', 'documents-professionnels', 'cours-python.txt']);
        $this->assertNotNull($result);
        $this->assertSame('cours-python.txt', $result->getName());
    }

    #[Test]
    public function findLocalLocatesNestedFile(): void
    {
        $result = $this->bash->findLocal(['home', 'mission', 'notes-enquete.txt']);
        $this->assertNotNull($result);
        $this->assertSame('notes-enquete.txt', $result->getName());
    }

    // =============================================
    // Cohérence des indices de l'escape game
    // =============================================

    #[Test]
    public function marseilleClueExistsInLocalFilesystem(): void
    {
        $email = $this->bash->findLocal(['home', 'preuves', 'email-intercepte.txt']);
        $this->assertNotNull($email);
        $this->assertStringContainsString('ville preferee', $email->getContent());
    }

    #[Test]
    public function marseilleClueExistsInHackerFilesystem(): void
    {
        $poem = $this->bash->find(['home', 'documents', 'documents-personnels', 'la-plus-belle-ville-du-monde.txt']);
        $this->assertNotNull($poem);
        $this->assertStringContainsString('Marseille', $poem->getContent());
    }
}
