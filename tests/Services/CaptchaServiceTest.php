<?php

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Services\CaptchaService;

class CaptchaServiceTest extends TestCase
{
    private CaptchaService $service;

    protected function setUp(): void
    {
        $this->service = new CaptchaService();
    }

    #[Test]
    public function generateNoiseLinesReturnsString(): void
    {
        $result = $this->service->generateNoiseLines(5, 140, 48);
        $this->assertIsString($result);
    }

    #[Test]
    public function generateNoiseLinesReturnsCorrectNumberOfLines(): void
    {
        $count = 7;
        $result = $this->service->generateNoiseLines($count, 140, 48);
        $this->assertSame($count, substr_count($result, '<line'));
    }

    #[Test]
    public function generateNoiseLinesContainsValidSvgMarkup(): void
    {
        $result = $this->service->generateNoiseLines(3, 140, 48);
        $this->assertStringContainsString('x1=', $result);
        $this->assertStringContainsString('y1=', $result);
        $this->assertStringContainsString('x2=', $result);
        $this->assertStringContainsString('y2=', $result);
        $this->assertStringContainsString('stroke=', $result);
        $this->assertStringContainsString('/>', $result);
    }

    #[Test]
    public function generateNoiseLinesWithZeroCountReturnsEmpty(): void
    {
        $result = $this->service->generateNoiseLines(0, 140, 48);
        $this->assertSame('', $result);
    }

    #[Test]
    public function generateNoiseLinesCoordinatesWithinBounds(): void
    {
        $width = 100;
        $height = 50;
        $result = $this->service->generateNoiseLines(20, $width, $height);

        // Extraire toutes les coordonnées via regex
        preg_match_all('/x1="(\d+)"/', $result, $x1Matches);
        preg_match_all('/y1="(\d+)"/', $result, $y1Matches);
        preg_match_all('/x2="(\d+)"/', $result, $x2Matches);
        preg_match_all('/y2="(\d+)"/', $result, $y2Matches);

        foreach ($x1Matches[1] as $x) {
            $this->assertGreaterThanOrEqual(0, (int)$x);
            $this->assertLessThanOrEqual($width, (int)$x);
        }
        foreach ($y1Matches[1] as $y) {
            $this->assertGreaterThanOrEqual(0, (int)$y);
            $this->assertLessThanOrEqual($height, (int)$y);
        }
        foreach ($x2Matches[1] as $x) {
            $this->assertGreaterThanOrEqual(0, (int)$x);
            $this->assertLessThanOrEqual($width, (int)$x);
        }
        foreach ($y2Matches[1] as $y) {
            $this->assertGreaterThanOrEqual(0, (int)$y);
            $this->assertLessThanOrEqual($height, (int)$y);
        }
    }

    #[Test]
    #[DataProvider('lineCountProvider')]
    public function generateNoiseLinesWithVariousCounts(int $count): void
    {
        $result = $this->service->generateNoiseLines($count, 200, 80);
        $this->assertSame($count, substr_count($result, '<line'));
    }

    public static function lineCountProvider(): array
    {
        return [
            '1 ligne'   => [1],
            '5 lignes'  => [5],
            '10 lignes' => [10],
            '50 lignes' => [50],
        ];
    }
}
