<?php

use PHPUnit\Framework\TestCase;
use Models\User\User;
use Tests\Mocks\MockDatabase;

class UserTest extends TestCase
{
    private User $user;
    private MockDatabase $mockDB;

    protected function setUp(): void
    {
        $this->mockDB = new MockDatabase();

        $this->user = new User();
        $ref = new ReflectionClass($this->user);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->user, $this->mockDB);
    }

    public function testSignupSuccess()
    {
        $this->mockDB->returnValue = true;

        $result = $this->user->signup([
            'prenom' => "John",
            'nom' => "Doe",
            'pseudo' => "john",
            'email' => "john@test.com",
            'password' => "hash"
        ]);

        $this->assertTrue($result);
    }

    public function testFindUserReturnsFalse()
    {
        $this->mockDB->singleReturn = null;

        $result = $this->user->findUserByEmailOrUsername("a@a.com", "test");

        $this->assertFalse($result);
    }

    public function testLoginSuccess()
    {
        $hashed = password_hash("secret", PASSWORD_DEFAULT);

        $this->mockDB->singleReturn = (object)[
            "password_hash" => $hashed
        ];

        $result = $this->user->login("test@test.com", "secret");
        $this->assertIsObject($result);
    }

    public function testLoginFailWrongPassword()
    {
        $this->mockDB->singleReturn = (object)[
            "password_hash" => password_hash("secret", PASSWORD_DEFAULT)
        ];

        $result = $this->user->login("test@test.com", "wrong");
        $this->assertFalse($result);
    }
}
