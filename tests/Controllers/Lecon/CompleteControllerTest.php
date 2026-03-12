<?php

namespace Tests\Controllers\Lecon;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Controllers\Lecon\Complete;

class CompleteControllerTest extends TestCase
{
    // =============================================
    // VALID_SLUGS
    // =============================================

    #[Test]
    public function validSlugsContainsThreeEntries(): void
    {
        $ref = new \ReflectionClass(Complete::class);
        $const = $ref->getConstant('VALID_SLUGS');
        $this->assertCount(3, $const);
    }

    #[Test]
    public function validSlugsContainsExpectedValues(): void
    {
        $ref = new \ReflectionClass(Complete::class);
        $const = $ref->getConstant('VALID_SLUGS');
        $this->assertContains('cesar', $const);
        $this->assertContains('vigenere', $const);
        $this->assertContains('permutation', $const);
    }

    #[Test]
    #[DataProvider('validSlugProvider')]
    public function validSlugIsAccepted(string $slug): void
    {
        $ref = new \ReflectionClass(Complete::class);
        $const = $ref->getConstant('VALID_SLUGS');
        $this->assertTrue(in_array($slug, $const));
    }

    public static function validSlugProvider(): array
    {
        return [
            'cesar'       => ['cesar'],
            'vigenere'    => ['vigenere'],
            'permutation' => ['permutation'],
        ];
    }

    #[Test]
    #[DataProvider('invalidSlugProvider')]
    public function invalidSlugIsRejected(string $slug): void
    {
        $ref = new \ReflectionClass(Complete::class);
        $const = $ref->getConstant('VALID_SLUGS');
        $this->assertFalse(in_array($slug, $const));
    }

    public static function invalidSlugProvider(): array
    {
        return [
            'vide'          => [''],
            'rgpd'          => ['rgpd'],
            'hist-mdp'      => ['hist-mdp'],
            'hamming'       => ['hamming'],
            'injection SQL' => ["cesar'; DROP TABLE--"],
            'majuscule'     => ['Cesar'],
        ];
    }

    // =============================================
    // Héritage et méthodes
    // =============================================

    #[Test]
    public function completeExtendsAbstractController(): void
    {
        $ref = new \ReflectionClass(Complete::class);
        $this->assertSame('Controllers\AbstractController', $ref->getParentClass()->getName());
    }

    #[Test]
    public function completeHasGetAndPostMethods(): void
    {
        $ref = new \ReflectionClass(Complete::class);
        $this->assertTrue($ref->hasMethod('getMethod'));
        $this->assertTrue($ref->hasMethod('postMethod'));
    }
}
