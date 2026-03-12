<?php

namespace Tests\Models\InterfaceMail;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Models\InterfaceMail\InterfaceMailModel;

class InterfaceMailModelTest extends TestCase
{
    private InterfaceMailModel $model;

    protected function setUp(): void
    {
        $this->model = new InterfaceMailModel();
    }

    #[Test]
    public function getEmailReturns5Emails(): void
    {
        $emails = $this->model->getemail();
        $this->assertCount(5, $emails);
    }

    #[Test]
    public function eachEmailHasRequiredKeys(): void
    {
        $requiredKeys = ['sender', 'subject', 'time', 'snippet', 'content'];
        foreach ($this->model->getemail() as $i => $email) {
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $email, "Email #$i manque la clé '$key'");
            }
        }
    }

    #[Test]
    public function firstEmailIsFromHacker(): void
    {
        $emails = $this->model->getemail();
        $this->assertSame('Le Hackeur', $emails[0]['sender']);
    }

    #[Test]
    public function emailContentIsNonEmpty(): void
    {
        foreach ($this->model->getemail() as $email) {
            $this->assertNotEmpty($email['content']);
            $this->assertNotEmpty($email['sender']);
            $this->assertNotEmpty($email['subject']);
        }
    }

    #[Test]
    public function getHackerEmailsReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->model->getHackerEmails());
    }
}
