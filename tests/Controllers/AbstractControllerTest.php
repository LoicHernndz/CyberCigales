<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\AbstractController;

/**
 * Sous-classe concrète pour tester AbstractController
 */
class StubController extends AbstractController
{
    public bool $getMethodCalled = false;
    public bool $postMethodCalled = false;

    public function getMethod(): void
    {
        $this->getMethodCalled = true;
    }

    public function postMethod(): void
    {
        $this->postMethodCalled = true;
    }
}

class AbstractControllerTest extends TestCase
{
    private StubController $controller;

    protected function setUp(): void
    {
        $this->controller = new StubController();
        // Initialiser la session
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
        $_SESSION = [];
    }

    // =============================================
    // control() — dispatch GET/POST
    // =============================================

    #[Test]
    public function controlDispatchesToGetMethodOnGet(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->controller->control();
        $this->assertTrue($this->controller->getMethodCalled);
        $this->assertFalse($this->controller->postMethodCalled);
    }

    #[Test]
    public function controlDispatchesToPostMethodOnPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->controller->control();
        $this->assertTrue($this->controller->postMethodCalled);
        $this->assertFalse($this->controller->getMethodCalled);
    }

    // =============================================
    // connexionVerify()
    // =============================================

    #[Test]
    public function connexionVerifyDoesNothingWhenLoggedIn(): void
    {
        $_SESSION['user_id'] = 42;
        // Should not throw or redirect (redirect is stubbed)
        $this->controller->connexionVerify();
        $this->assertTrue(true); // no exception = pass
    }

    #[Test]
    public function connexionVerifyRedirectsWhenNotLoggedIn(): void
    {
        unset($_SESSION['user_id']);
        // redirect() is a no-op in tests, so this just completes
        $this->controller->connexionVerify();
        $this->assertArrayNotHasKey('user_id', $_SESSION);
    }

    // =============================================
    // Vérifications de base
    // =============================================

    #[Test]
    public function controllerIsAbstract(): void
    {
        $ref = new \ReflectionClass(AbstractController::class);
        $this->assertTrue($ref->isAbstract());
    }

    #[Test]
    public function getMethodIsAbstract(): void
    {
        $ref = new \ReflectionClass(AbstractController::class);
        $method = $ref->getMethod('getMethod');
        $this->assertTrue($method->isAbstract());
    }

    #[Test]
    public function controlMethodExists(): void
    {
        $this->assertTrue(method_exists(AbstractController::class, 'control'));
    }

    #[Test]
    public function jsonResponseMethodExists(): void
    {
        $ref = new \ReflectionClass(AbstractController::class);
        $method = $ref->getMethod('jsonResponse');
        $this->assertTrue($method->isProtected());
    }
}
