<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Tag;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class TagControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoutes();
    }

    #[Test]
    public function it_can_store_a_tag(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.tags.store'), [
                'name' => 'Laravel',
                'description' => 'Laravel framework discussions',
                'color' => '#ffffff',
                'background_color' => '#ff5733',
            ]);

        $this->assertDatabaseHas('forum_tags', [
            'name' => 'Laravel',
        ]);
    }

    #[Test]
    public function it_generates_slug_when_storing(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.tags.store'), [
                'name' => 'New Tag',
            ]);

        $tag = Tag::where('name', 'New Tag')->first();
        $this->assertNotNull($tag);
        $this->assertStringContainsString('new-tag', $tag->slug);
    }

    #[Test]
    public function it_validates_name_when_storing(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.tags.store'), [
                'name' => '',
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function it_validates_unique_name(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        Tag::create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $this->actingAs($user)
            ->post(route('forum.tags.store'), [
                'name' => 'PHP',
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function it_validates_color_format(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.tags.store'), [
                'name' => 'Test Tag',
                'color' => 'invalid-color',
            ])
            ->assertSessionHasErrors('color');
    }

    #[Test]
    public function it_can_update_a_tag(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $tag = Tag::create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $this->actingAs($user)
            ->put(route('forum.tags.update', ['tag' => $tag]), [
                'name' => 'PHP 8',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('forum_tags', [
            'id' => $tag->id,
            'name' => 'PHP 8',
        ]);
    }

    #[Test]
    public function it_generates_new_slug_when_name_changes(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $tag = Tag::create([
            'name' => 'Old Name',
            'slug' => 'old-name-1',
        ]);

        $this->actingAs($user)
            ->put(route('forum.tags.update', ['tag' => $tag]), [
                'name' => 'New Name',
            ]);

        $tag->refresh();
        $this->assertStringContainsString('new-name', $tag->slug);
    }

    #[Test]
    public function it_can_delete_a_tag(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $tag = Tag::create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $this->actingAs($user)
            ->delete(route('forum.tags.destroy', ['tag' => $tag]));

        $this->assertSoftDeleted('forum_tags', [
            'id' => $tag->id,
        ]);
    }
}
