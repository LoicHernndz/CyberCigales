<?php

namespace Tests\Services\Code;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Services\Code\Hamming;

class HammingTest extends TestCase
{
    // =============================================
    // generateSquare : calcul des bits de parité
    // =============================================

    #[Test]
    #[DataProvider('squareProvider')]
    public function generateSquareCalculatesCorrectParity(array $dataBits, array $expectedSquare): void
    {
        $this->assertSame($expectedSquare, Hamming::generateSquare($dataBits));
    }

    public static function squareProvider(): array
    {
        return [
            'tous à 0' => [
                [0, 0, 0, 0],
                [[0, 0, 0], [0, 0, 0], [0, 0, 0]],
            ],
            'tous à 1' => [
                [1, 1, 1, 1],
                [[1, 1, 0], [1, 1, 0], [0, 0, 0]],
            ],
            'd1=1 seul' => [
                [1, 0, 0, 0],
                [[1, 0, 1], [0, 0, 0], [1, 0, 1]],
            ],
            'd2=1 seul' => [
                [0, 1, 0, 0],
                [[0, 1, 1], [0, 0, 0], [0, 1, 1]],
            ],
            'd3=1 seul' => [
                [0, 0, 1, 0],
                [[0, 0, 0], [1, 0, 1], [1, 0, 1]],
            ],
            'd4=1 seul' => [
                [0, 0, 0, 1],
                [[0, 0, 0], [0, 1, 1], [0, 1, 1]],
            ],
            'd1=1, d4=1' => [
                [1, 0, 0, 1],
                [[1, 0, 1], [0, 1, 1], [1, 1, 0]],
            ],
            'd1=1, d2=1, d3=1' => [
                [1, 1, 1, 0],
                [[1, 1, 0], [1, 0, 1], [0, 1, 1]],
            ],
        ];
    }

    // =============================================
    // Structure du carré
    // =============================================

    #[Test]
    public function generateSquareReturns3x3Array(): void
    {
        $square = Hamming::generateSquare([1, 0, 1, 0]);
        $this->assertCount(3, $square);
        foreach ($square as $row) {
            $this->assertCount(3, $row);
        }
    }

    #[Test]
    public function generateSquareContainsOnlyBinaryValues(): void
    {
        $square = Hamming::generateSquare([1, 1, 0, 1]);
        foreach ($square as $row) {
            foreach ($row as $cell) {
                $this->assertContains($cell, [0, 1]);
            }
        }
    }

    // =============================================
    // Vérification mathématique des parités
    // =============================================

    #[Test]
    #[DataProvider('parityCheckProvider')]
    public function parityConstraintsAreSatisfied(array $dataBits): void
    {
        $s = Hamming::generateSquare($dataBits);

        // p1 = parité ligne 1 : d1 + d2 + p1 ≡ 0 (mod 2)
        $this->assertSame(0, ($s[0][0] + $s[0][1] + $s[0][2]) % 2, 'Parité ligne 1');

        // p2 = parité ligne 2 : d3 + d4 + p2 ≡ 0 (mod 2)
        $this->assertSame(0, ($s[1][0] + $s[1][1] + $s[1][2]) % 2, 'Parité ligne 2');

        // p3 = parité colonne 1 : d1 + d3 + p3 ≡ 0 (mod 2)
        $this->assertSame(0, ($s[0][0] + $s[1][0] + $s[2][0]) % 2, 'Parité colonne 1');

        // p4 = parité colonne 2 : d2 + d4 + p4 ≡ 0 (mod 2)
        $this->assertSame(0, ($s[0][1] + $s[1][1] + $s[2][1]) % 2, 'Parité colonne 2');

        // p5 = parité globale : somme de tous ≡ 0 (mod 2)
        $totalSum = 0;
        foreach ($s as $row) {
            foreach ($row as $cell) {
                $totalSum += $cell;
            }
        }
        $this->assertSame(0, $totalSum % 2, 'Parité globale');
    }

    public static function parityCheckProvider(): array
    {
        return [
            'tous 0'     => [[0, 0, 0, 0]],
            'tous 1'     => [[1, 1, 1, 1]],
            '1,0,0,0'    => [[1, 0, 0, 0]],
            '0,1,0,0'    => [[0, 1, 0, 0]],
            '0,0,1,0'    => [[0, 0, 1, 0]],
            '0,0,0,1'    => [[0, 0, 0, 1]],
            '1,0,1,0'    => [[1, 0, 1, 0]],
            '0,1,0,1'    => [[0, 1, 0, 1]],
            '1,1,0,0'    => [[1, 1, 0, 0]],
            '0,0,1,1'    => [[0, 0, 1, 1]],
            '1,0,0,1'    => [[1, 0, 0, 1]],
            '0,1,1,0'    => [[0, 1, 1, 0]],
            '1,1,1,0'    => [[1, 1, 1, 0]],
            '1,1,0,1'    => [[1, 1, 0, 1]],
            '1,0,1,1'    => [[1, 0, 1, 1]],
        ];
    }

    // =============================================
    // generateRandomSquare
    // =============================================

    #[Test]
    public function generateRandomSquareReturnsSquareAndDataBits(): void
    {
        $result = Hamming::generateRandomSquare();
        $this->assertArrayHasKey('square', $result);
        $this->assertArrayHasKey('dataBits', $result);
        $this->assertCount(4, $result['dataBits']);
        $this->assertCount(3, $result['square']);
    }

    #[Test]
    public function generateRandomSquareIsConsistentWithGenerateSquare(): void
    {
        $result = Hamming::generateRandomSquare();
        $recomputed = Hamming::generateSquare($result['dataBits']);
        $this->assertSame($recomputed, $result['square']);
    }

    // =============================================
    // generateSquareWithError
    // =============================================

    #[Test]
    public function generateSquareWithErrorReturnsRequiredKeys(): void
    {
        $result = Hamming::generateSquareWithError();
        $this->assertArrayHasKey('square', $result);
        $this->assertArrayHasKey('errorPosition', $result);
        $this->assertArrayHasKey('originalSquare', $result);
        $this->assertArrayHasKey('row', $result['errorPosition']);
        $this->assertArrayHasKey('col', $result['errorPosition']);
    }

    #[Test]
    public function generateSquareWithErrorHasExactlyOneBitDifference(): void
    {
        $original = Hamming::generateSquare([1, 0, 1, 0]);
        $result = Hamming::generateSquareWithError($original);

        $differences = 0;
        for ($r = 0; $r < 3; $r++) {
            for ($c = 0; $c < 3; $c++) {
                if ($result['square'][$r][$c] !== $result['originalSquare'][$r][$c]) {
                    $differences++;
                }
            }
        }
        $this->assertSame(1, $differences, 'Il doit y avoir exactement 1 bit différent');
    }

    #[Test]
    public function generateSquareWithErrorPositionMatchesDifference(): void
    {
        $original = Hamming::generateSquare([0, 1, 1, 0]);
        $result = Hamming::generateSquareWithError($original);

        $errorRow = $result['errorPosition']['row'];
        $errorCol = $result['errorPosition']['col'];

        // Le bit à la position d'erreur doit être inversé
        $this->assertNotSame(
            $result['originalSquare'][$errorRow][$errorCol],
            $result['square'][$errorRow][$errorCol],
            'Le bit à la position d\'erreur doit être inversé'
        );
    }

    #[Test]
    public function generateSquareWithErrorFromNullGeneratesValidSquare(): void
    {
        // Quand on passe null, un carré aléatoire est généré
        $result = Hamming::generateSquareWithError(null);
        $this->assertCount(3, $result['square']);
        $this->assertCount(3, $result['originalSquare']);
    }
}
