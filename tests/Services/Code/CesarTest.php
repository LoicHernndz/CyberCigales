<?php

namespace Tests\Services\Code;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Services\Code\Cesar;

class CesarTest extends TestCase
{
    // =============================================
    // Chiffrement
    // =============================================

    #[Test]
    #[DataProvider('encryptProvider')]
    public function encryptProducesExpectedOutput(string $text, int $shift, string $expected): void
    {
        $this->assertSame($expected, Cesar::encrypt($text, $shift));
    }

    public static function encryptProvider(): array
    {
        return [
            'décalage 3 basique'       => ['abc', 3, 'def'],
            'wraparound xyz+3'         => ['xyz', 3, 'abc'],
            'décalage 0 = identité'    => ['abc', 0, 'abc'],
            'rotation complète 26'     => ['abc', 26, 'abc'],
            'décalage > 26 (mod)'      => ['abc', 29, 'def'],
            'décalage négatif'         => ['abc', -3, 'xyz'],
            'ROT13'                    => ['Hello World!', 13, 'uryybjbeyq'],
            'chiffres préservés'       => ['abc123', 3, 'def123'],
            'chaîne vide'              => ['', 5, ''],
        ];
    }

    // =============================================
    // Déchiffrement
    // =============================================

    #[Test]
    #[DataProvider('decryptProvider')]
    public function decryptProducesExpectedOutput(string $text, int $shift, string $expected): void
    {
        $this->assertSame($expected, Cesar::decrypt($text, $shift));
    }

    public static function decryptProvider(): array
    {
        return [
            'décalage 3 basique'    => ['def', 3, 'abc'],
            'décalage 0 = identité' => ['abc', 0, 'abc'],
            'chaîne vide'           => ['', 5, ''],
        ];
    }

    // =============================================
    // Roundtrip (chiffrer puis déchiffrer = original)
    // =============================================

    #[Test]
    #[DataProvider('roundtripProvider')]
    public function roundtripPreservesText(string $text, int $shift): void
    {
        $encrypted = Cesar::encrypt($text, $shift);
        $decrypted = Cesar::decrypt($encrypted, $shift);
        // cleanText normalise le texte, on compare donc avec la version nettoyée
        $cleaned = $this->callCleanText($text);
        $this->assertSame($cleaned, $decrypted);
    }

    public static function roundtripProvider(): array
    {
        return [
            'abc avec décalage 3'      => ['abc', 3],
            'xyz avec décalage 5'      => ['xyz', 5],
            'texte long'               => ['bonjour le monde', 17],
            'décalage négatif'         => ['hello', -7],
            'décalage nul'             => ['test', 0],
            'avec chiffres'            => ['abc123xyz', 10],
        ];
    }

    // =============================================
    // Verification
    // =============================================

    #[Test]
    public function verificationReturnsTrueWhenCorrect(): void
    {
        $this->assertTrue(Cesar::verification('abc', 'def', 'encrypt', 3));
    }

    #[Test]
    public function verificationReturnsFalseWhenIncorrect(): void
    {
        $this->assertFalse(Cesar::verification('abc', 'zzz', 'encrypt', 3));
    }

    // =============================================
    // Helpers
    // =============================================

    private function callCleanText(string $text): string
    {
        $ref = new \ReflectionMethod(Cesar::class, 'cleanText');
        return $ref->invoke(null, $text);
    }
}
