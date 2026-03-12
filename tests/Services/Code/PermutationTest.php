<?php

namespace Tests\Services\Code;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Services\Code\Permutation;

class PermutationTest extends TestCase
{
    // =============================================
    // Chiffrement
    // =============================================

    #[Test]
    #[DataProvider('encryptProvider')]
    public function encryptProducesExpectedOutput(string $text, string $key, string $spaceChar, string $expected): void
    {
        $this->assertSame($expected, Permutation::encrypt($text, $key, $spaceChar));
    }

    public static function encryptProvider(): array
    {
        return [
            'bonjour avec clé cle'  => ['bonjour', 'cle', 'x', 'bjrnuxoox'],
            'hello avec clé abc'    => ['hello', 'abc', 'x', 'hleolx'],
            'test avec clé ab'      => ['test', 'ab', 'x', 'tset'],
            'chaîne vide'           => ['', 'abc', 'x', ''],
        ];
    }

    // =============================================
    // Déchiffrement
    // =============================================

    #[Test]
    public function decryptRecoversOriginalTextWithPadding(): void
    {
        $encrypted = Permutation::encrypt('bonjour', 'cle', 'x');
        $decrypted = Permutation::decrypt($encrypted, 'cle', 'x');
        $this->assertSame('bonjourxx', $decrypted);
    }

    #[Test]
    public function decryptRoundtripPreservesTextWithPadding(): void
    {
        $text = 'hello';
        $key = 'abc';
        $spaceChar = 'x';

        $encrypted = Permutation::encrypt($text, $key, $spaceChar);
        $decrypted = Permutation::decrypt($encrypted, $key, $spaceChar);

        $this->assertStringStartsWith('hello', $decrypted);
    }

    #[Test]
    public function decryptDoesNotProduceWarning(): void
    {
        $encrypted = Permutation::encrypt('test', 'abc', 'x');

        $warningTriggered = false;
        set_error_handler(function (int $errno) use (&$warningTriggered) {
            if ($errno === E_WARNING) {
                $warningTriggered = true;
            }
            return true;
        });

        Permutation::decrypt($encrypted, 'abc', 'x');
        restore_error_handler();

        $this->assertFalse($warningTriggered, 'decrypt ne devrait plus émettre de warning');
    }

    // =============================================
    // Cas limites
    // =============================================

    #[Test]
    public function encryptWithTextMultipleOfKeyLengthNoPadding(): void
    {
        // 'abcdef' = 6 chars, clé 'abc' = 3 chars → pas de padding
        $result = Permutation::encrypt('abcdef', 'abc', 'x');
        $this->assertSame(6, strlen($result));
    }

    #[Test]
    public function encryptPadsTextToMultipleOfKeyLength(): void
    {
        // 'hello' = 5 chars, clé 'abc' = 3 chars → padding à 6
        $result = Permutation::encrypt('hello', 'abc', 'x');
        $this->assertSame(6, strlen($result));
    }

    #[Test]
    public function encryptReplacesSpacesWithSpaceChar(): void
    {
        // Les espaces sont remplacés par le space_char avant le nettoyage
        $result = Permutation::encrypt('ab cd', 'ab', 'x');
        $this->assertStringNotContainsString(' ', $result);
    }

    // =============================================
    // Verification
    // =============================================

    #[Test]
    public function verificationReturnsTrueWhenCorrect(): void
    {
        $this->assertTrue(
            Permutation::verification('bonjour', 'bjrnuxoox', 'encrypt', 'cle', 'x')
        );
    }

    #[Test]
    public function verificationReturnsFalseWhenIncorrect(): void
    {
        $this->assertFalse(
            Permutation::verification('bonjour', 'zzzzzzzzz', 'encrypt', 'cle', 'x')
        );
    }
}
