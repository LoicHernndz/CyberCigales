<?php

namespace Tests\Models\Game;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Models\Game\ResetProgress;
use Tests\Mocks\MockDatabase;

class ResetProgressTest extends TestCase
{
    private ResetProgress $resetProgress;
    private MockDatabase $mockDB;

    protected function setUp(): void
    {
        $this->mockDB = new MockDatabase();
        $this->resetProgress = new ResetProgress();

        $ref = new \ReflectionClass($this->resetProgress);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->resetProgress, $this->mockDB);
    }

    #[Test]
    public function resetAllProgressReturnsTrueOnSuccess(): void
    {
        $this->mockDB->returnValue = true;
        $result = $this->resetProgress->resetAllProgress(1);
        $this->assertTrue($result);
    }

    #[Test]
    public function resetAllProgressExecutesDeleteQueries(): void
    {
        $this->mockDB->returnValue = true;
        $this->resetProgress->resetAllProgress(1);

        // Vérifie qu'au moins une query DELETE a été exécutée
        $hasDelete = false;
        foreach ($this->mockDB->queries as $query) {
            if (str_contains($query, 'DELETE') || str_contains($query, 'UPDATE')) {
                $hasDelete = true;
                break;
            }
        }
        $this->assertTrue($hasDelete, 'resetAllProgress devrait exécuter des queries DELETE/UPDATE');
    }

    #[Test]
    public function resetAllProgressClearsSessionVariable(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['instagram_mel_post_unlocked'] = true;

        $this->mockDB->returnValue = true;
        $this->resetProgress->resetAllProgress(1);

        $this->assertArrayNotHasKey('instagram_mel_post_unlocked', $_SESSION);
    }

    #[Test]
    public function resetAllProgressBindsUserId(): void
    {
        $this->mockDB->returnValue = true;
        $this->resetProgress->resetAllProgress(42);

        $this->assertArrayHasKey(':user_id', $this->mockDB->params);
        $this->assertSame(42, $this->mockDB->params[':user_id']);
    }
}
