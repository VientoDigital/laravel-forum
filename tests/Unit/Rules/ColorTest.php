<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Rules;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Vientodigital\LaravelForum\Rules\Color;

class ColorTest extends TestCase
{
    private Color $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new Color();
    }

    #[Test]
    public function it_passes_for_empty_value(): void
    {
        $this->assertTrue($this->rule->passes('color', ''));
        $this->assertTrue($this->rule->passes('color', null));
    }

    #[Test]
    #[DataProvider('validHexColorsProvider')]
    public function it_passes_for_valid_hex_colors(string $color): void
    {
        $this->assertTrue($this->rule->passes('color', $color));
    }

    public static function validHexColorsProvider(): array
    {
        return [
            'three digit hex' => ['#fff'],
            'three digit hex uppercase' => ['#FFF'],
            'three digit hex mixed' => ['#fFf'],
            'six digit hex' => ['#ffffff'],
            'six digit hex uppercase' => ['#FFFFFF'],
            'six digit hex color' => ['#ff5733'],
            'six digit hex another' => ['#00ff00'],
        ];
    }

    #[Test]
    #[DataProvider('validRgbaColorsProvider')]
    public function it_passes_for_valid_rgba_colors(string $color): void
    {
        $this->assertTrue($this->rule->passes('color', $color));
    }

    public static function validRgbaColorsProvider(): array
    {
        return [
            'rgba with alpha' => ['rgba(255,255,255,0.5)'],
            'rgba full opacity' => ['rgba(0,0,0,1)'],
            'rgba with spaces' => ['rgba(128, 64, 32, 0.8)'],
        ];
    }

    #[Test]
    #[DataProvider('validHslColorsProvider')]
    public function it_passes_for_valid_hsl_colors(string $color): void
    {
        $this->assertTrue($this->rule->passes('color', $color));
    }

    public static function validHslColorsProvider(): array
    {
        return [
            'hsl basic' => ['hsl(360, 100%, 50%)'],
            'hsl zeros' => ['hsl(0, 0%, 0%)'],
            'hsla with alpha' => ['hsla(180, 50%, 50%, 0.5)'],
        ];
    }

    #[Test]
    #[DataProvider('invalidColorsProvider')]
    public function it_fails_for_invalid_colors(string $color): void
    {
        $this->assertFalse($this->rule->passes('color', $color));
    }

    public static function invalidColorsProvider(): array
    {
        return [
            'no hash' => ['fff'],
            'invalid hex length' => ['#ffff'],
            'invalid characters' => ['#gggggg'],
            'word color' => ['red'],
            'invalid rgb' => ['rgb(256, 0, 0)'],
        ];
    }

    #[Test]
    public function it_returns_correct_error_message(): void
    {
        $this->assertEquals('The :attribute must be an a valid color.', $this->rule->message());
    }
}
