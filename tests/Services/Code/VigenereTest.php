<?php

namespace Tests\Services\Code;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Services\Code\Vigenere;

/**
 * Tests unitaires pour l'algorithme de Vigenère
 */
class VigenereTest extends TestCase
{
    // =============================================
    // Chiffrement — valeurs réelles actuelles
    // =============================================

    #[Test]
    #[DataProvider('encryptProvider')]
    public function encryptProducesExpectedOutput(string $text, string $key, string $expected): void
    {
        $this->assertSame($expected, Vigenere::encrypt($text, $key));
    }

    public static function encryptProvider(): array
    {
        return [
            'bonjour avec clé cle'  => ['bonjour', 'cle', 'DZRLZYT'],
            'test123 avec clé abc'  => ['test123', 'abc', 'TFUT123'],
            'aaa avec clé a'        => ['aaa', 'a', 'AAA'],
            'chaîne vide'           => ['', 'abc', ''],
        ];
    }

    // =============================================
    // Déchiffrement — valeurs réelles actuelles
    // =============================================

    #[Test]
    #[DataProvider('decryptProvider')]
    public function decryptProducesExpectedOutput(string $text, string $key, string $expected): void
    {
        $this->assertSame($expected, Vigenere::decrypt($text, $key));
    }

    public static function decryptProvider(): array
    {
        return [
            'DZRLZYT avec clé cle'  => ['DZRLZYT', 'cle', 'BONJOUR'],
            'AAA avec clé a'        => ['AAA', 'a', 'AAA'],
            'chaîne vide'           => ['', 'abc', ''],
        ];
    }

    // =============================================
    // Roundtrip (encrypt → decrypt)
    // =============================================

    #[Test]
    public function roundtripWorksWithSimpleInput(): void
    {
        $encrypted = Vigenere::encrypt('aaa', 'a');
        $this->assertSame('AAA', $encrypted);

        $decrypted = Vigenere::decrypt($encrypted, 'a');
        $this->assertSame('AAA', $decrypted);
    }

    #[Test]
    public function roundtripWorksWithComplexInput(): void
    {
        $encrypted = Vigenere::encrypt('bonjour', 'cle');
        $this->assertSame('DZRLZYT', $encrypted);

        $decrypted = Vigenere::decrypt($encrypted, 'cle');
        $this->assertSame('BONJOUR', $decrypted);
    }

    // =============================================
    // Chiffres préservés
    // =============================================

    #[Test]
    public function encryptPreservesDigits(): void
    {
        $result = Vigenere::encrypt('test123', 'abc');
        $this->assertStringContainsString('123', $result);
    }

    // =============================================
    // Verification
    // =============================================

    #[Test]
    public function verificationReturnsTrueWhenCorrect(): void
    {
        $this->assertTrue(Vigenere::verification('bonjour', 'DZRLZYT', 'encrypt', 'cle'));
    }

    #[Test]
    public function verificationReturnsFalseWhenIncorrect(): void
    {
        $this->assertFalse(Vigenere::verification('bonjour', 'ZZZZZZZ', 'encrypt', 'cle'));
    }
}
