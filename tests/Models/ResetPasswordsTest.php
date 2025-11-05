<?php

use PHPUnit\Framework\TestCase;
use Models\User\ResetPasswords;
use Tests\Mocks\MockDatabase;

class ResetPasswordsTest extends TestCase
{
    private ResetPasswords $resetPasswords;
    private MockDatabase $mockDB;

    protected function setUp(): void
    {
        $this->mockDB = new MockDatabase();

        // Injecter le mock en remplaçant Database
        $this->resetPasswords = new ResetPasswords();
        $this->resetPasswordsReflection = new ReflectionClass($this->resetPasswords);
        $prop = $this->resetPasswordsReflection->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->resetPasswords, $this->mockDB);
    }

    public function testDeleteEmail()
    {
        $this->mockDB->returnValue = true;

        $result = $this->resetPasswords->deleteEmail("test@test.com");

        $this->assertTrue($result);
        $this->assertStringContainsString("DELETE FROM pwdreset", $this->mockDB->queries[0]);
    }

    public function testInsertToken()
    {
        $this->mockDB->returnValue = true;

        $result = $this->resetPasswords->insertToken(
            "test@test.com", "selector", "hashed", time()
        );

        $this->assertTrue($result);
        $this->assertStringContainsString("INSERT INTO pwdreset", $this->mockDB->queries[0]);
    }

    public function testResetPasswordSuccess()
    {
        $this->mockDB->singleReturn = (object)["id" => 1];

        $result = $this->resetPasswords->resetPassword("selector", time());

        $this->assertIsObject($result);
        $this->assertEquals(1, $result->id);
    }

    public function testResetPasswordFail()
    {
        $this->mockDB->singleReturn = null;

        $result = $this->resetPasswords->resetPassword("selector", time());

        $this->assertFalse($result);
    }
}
