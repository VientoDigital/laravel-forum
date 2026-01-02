<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Tag;
use Vientodigital\LaravelForum\Tests\TestCase;

class TagTest extends TestCase
{
    #[Test]
    public function it_can_create_a_tag(): void
    {
        $tag = Tag::create([
            'name' => 'General Discussion',
            'slug' => 'general-discussion',
            'description' => 'General topics',
            'color' => '#ffffff',
            'background_color' => '#007bff',
        ]);

        $this->assertDatabaseHas('forum_tags', [
            'name' => 'General Discussion',
            'slug' => 'general-discussion',
        ]);
    }

    #[Test]
    public function it_uses_correct_table_name(): void
    {
        $tag = new Tag();
        $this->assertEquals('forum_tags', $tag->getTable());
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $tag = new Tag();
        $fillable = $tag->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('slug', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('color', $fillable);
        $this->assertContains('background_color', $fillable);
        $this->assertContains('discussion_count', $fillable);
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $tag = Tag::create([
            'name' => 'To Delete',
            'slug' => 'to-delete',
        ]);

        $tag->delete();

        $this->assertSoftDeleted('forum_tags', [
            'id' => $tag->id,
        ]);
    }

    #[Test]
    public function it_can_store_colors(): void
    {
        $tag = Tag::create([
            'name' => 'Colored Tag',
            'slug' => 'colored-tag',
            'color' => '#ff5733',
            'background_color' => '#333333',
        ]);

        $this->assertEquals('#ff5733', $tag->color);
        $this->assertEquals('#333333', $tag->background_color);
    }
}
