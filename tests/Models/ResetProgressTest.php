<?php

use PHPUnit\Framework\TestCase;
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
        $ref = new ReflectionClass($this->resetProgress);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->resetProgress, $this->mockDB);
    }

    public function testResetAllProgressExecutesQueries()
    {
        $userId = 42;

        $result = $this->resetProgress->resetAllProgress($userId);

        $this->assertTrue($result);

        // Verify that 3 queries were executed
        $this->assertCount(3, $this->mockDB->queries);

        $this->assertStringContainsString('DELETE FROM user_chat_progress', $this->mockDB->queries[0]);
        $this->assertStringContainsString('DELETE FROM cypher_scores', $this->mockDB->queries[1]);
        $this->assertStringContainsString('UPDATE users SET score = 0', $this->mockDB->queries[2]);

        // Verify that the user ID was bound correctly
        $this->assertEquals($userId, $this->mockDB->params[':user_id']);
    }
}
