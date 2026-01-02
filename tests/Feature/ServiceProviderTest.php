<?php

namespace Vientodigital\LaravelForum\Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_config(): void
    {
        $this->assertNotNull(config('laravel-forum'));
        $this->assertIsArray(config('laravel-forum.table_names'));
    }

    #[Test]
    public function it_registers_livewire_components(): void
    {
        $this->assertTrue(
            class_exists(\Vientodigital\LaravelForum\Http\Livewire\Forum\Comment::class)
        );
        $this->assertTrue(
            class_exists(\Vientodigital\LaravelForum\Http\Livewire\Forum\CommentEdit::class)
        );
        $this->assertTrue(
            class_exists(\Vientodigital\LaravelForum\Http\Livewire\Forum\Comments::class)
        );
    }

    #[Test]
    public function it_registers_str_initials_macro(): void
    {
        $this->assertTrue(Str::hasMacro('initials'));
        $this->assertEquals('JD', Str::initials('John Doe'));
        $this->assertEquals('JDS', Str::initials('John Doe Smith', 3));
    }

    #[Test]
    public function it_registers_stringable_initials_macro(): void
    {
        $result = Str::of('John Doe')->initials();
        $this->assertEquals('JD', (string) $result);
    }

    #[Test]
    public function it_loads_views(): void
    {
        $this->assertTrue(
            view()->exists('laravel-forum::tw.livewire.forum.comment')
        );
    }

    #[Test]
    public function it_loads_translations(): void
    {
        $this->assertNotNull(trans('laravel-forum::words'));
    }
}
