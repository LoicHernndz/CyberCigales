<?php

use PHPUnit\Framework\TestCase;
use Models\User\UserStats;
use Tests\Mocks\MockDatabase;

class UserStatsTest extends TestCase
{
    private UserStats $stats;
    private MockDatabase $mockDB;

    protected function setUp(): void
    {
        $this->mockDB = new MockDatabase();

        $this->stats = new UserStats();
        $ref = new ReflectionClass($this->stats);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->stats, $this->mockDB);
    }

    public function testGetUserStatsReturnsArray()
    {
        $this->mockDB->singleReturn = (object) [
            'id' => 1,
            'prenom' => 'Test',
            'nom' => 'User',
            'pseudo' => 'test',
            'email' => 'a@a.com',
            'total_score' => 10,
            'date_inscription' => '2024-01-01'
        ];

        $result = $this->stats->getUserStats(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('general', $result);
        $this->assertArrayHasKey('cypher', $result);
    }
}

