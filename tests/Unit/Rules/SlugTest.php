<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Rules;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Vientodigital\LaravelForum\Rules\Slug;

class SlugTest extends TestCase
{
    private Slug $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new Slug();
    }

    #[Test]
    public function it_passes_for_empty_value(): void
    {
        $this->assertTrue($this->rule->passes('slug', ''));
        $this->assertTrue($this->rule->passes('slug', null));
    }

    #[Test]
    #[DataProvider('validSlugsProvider')]
    public function it_passes_for_valid_slugs(string $slug): void
    {
        $this->assertTrue($this->rule->passes('slug', $slug));
    }

    public static function validSlugsProvider(): array
    {
        return [
            'simple word' => ['hello'],
            'with numbers' => ['hello123'],
            'with dash' => ['hello-world'],
            'multiple dashes' => ['hello-world-test'],
            'numbers after dash' => ['test-123'],
            'single letter' => ['a'],
            'letter with number' => ['a1'],
        ];
    }

    #[Test]
    #[DataProvider('invalidSlugsProvider')]
    public function it_fails_for_invalid_slugs(string $slug): void
    {
        $this->assertFalse($this->rule->passes('slug', $slug));
    }

    public static function invalidSlugsProvider(): array
    {
        return [
            'starts with number' => ['123hello'],
            'starts with dash' => ['-hello'],
            'uppercase letters' => ['Hello'],
            'contains spaces' => ['hello world'],
            'contains underscore' => ['hello_world'],
            'contains special chars' => ['hello@world'],
            'only numbers' => ['123'],
        ];
    }

    #[Test]
    public function it_returns_correct_error_message(): void
    {
        $this->assertEquals(
            'The :attribute can have letters (A-Z), numbers (0-9) or dashes (-).',
            $this->rule->message()
        );
    }
}
