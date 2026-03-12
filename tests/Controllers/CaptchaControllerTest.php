<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Controllers\Captcha;

class CaptchaControllerTest extends TestCase
{
    private \ReflectionMethod $generateCode;

    protected function setUp(): void
    {
        $this->generateCode = new \ReflectionMethod(Captcha::class, 'generateCode');
        $this->generateCode->setAccessible(true);
    }

    // =============================================
    // generateCode()
    // =============================================

    #[Test]
    public function generateCodeReturnsStringOfLength5(): void
    {
        $captcha = new Captcha();
        $code = $this->generateCode->invoke($captcha);
        $this->assertSame(5, strlen($code));
    }

    #[Test]
    public function generateCodeUsesOnlyAllowedCharacters(): void
    {
        $allowed = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $captcha = new Captcha();

        // Tester 50 codes pour couvrir la randomness
        for ($i = 0; $i < 50; $i++) {
            $code = $this->generateCode->invoke($captcha);
            foreach (str_split($code) as $char) {
                $this->assertStringContainsString($char, $allowed, "Caractère '$char' non autorisé");
            }
        }
    }

    #[Test]
    public function generateCodeExcludesConfusingCharacters(): void
    {
        $excluded = ['0', 'O', '1', 'I'];
        $captcha = new Captcha();

        // Tester 100 codes pour vérifier l'exclusion
        for ($i = 0; $i < 100; $i++) {
            $code = $this->generateCode->invoke($captcha);
            foreach ($excluded as $char) {
                $this->assertStringNotContainsString($char, $code, "Caractère confus '$char' trouvé dans '$code'");
            }
        }
    }

    #[Test]
    public function generateCodeProducesDifferentCodes(): void
    {
        $captcha = new Captcha();
        $codes = [];
        for ($i = 0; $i < 20; $i++) {
            $codes[] = $this->generateCode->invoke($captcha);
        }
        // Au moins 2 codes différents sur 20 (probabilité 1 - (1/32^5)^19 ≈ 1)
        $unique = array_unique($codes);
        $this->assertGreaterThan(1, count($unique), 'Les codes devraient être différents');
    }

    // =============================================
    // Héritage
    // =============================================

    #[Test]
    public function captchaExtendsAbstractController(): void
    {
        $ref = new \ReflectionClass(Captcha::class);
        $this->assertSame('Controllers\AbstractController', $ref->getParentClass()->getName());
    }
}
