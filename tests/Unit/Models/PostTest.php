<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class PostTest extends TestCase
{
    #[Test]
    public function it_can_create_a_post(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $user->id,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $this->assertDatabaseHas('forum_posts', [
            'content' => 'Test content',
            'number' => 1,
        ]);
    }

    #[Test]
    public function it_uses_correct_table_name(): void
    {
        $post = new Post();
        $this->assertEquals('forum_posts', $post->getTable());
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $post = new Post();
        $fillable = $post->getFillable();

        $this->assertContains('discussion_id', $fillable);
        $this->assertContains('user_id', $fillable);
        $this->assertContains('content', $fillable);
        $this->assertContains('number', $fillable);
        $this->assertContains('ip_address', $fillable);
    }

    #[Test]
    public function it_belongs_to_a_discussion(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $user->id,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $this->assertInstanceOf(Discussion::class, $post->discussion);
        $this->assertEquals($discussion->id, $post->discussion->id);
    }

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $user->id,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    #[Test]
    public function it_can_check_if_user_can_edit(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);

        $otherUser = User::create([
            'name' => 'Other',
            'email' => 'other@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $owner->id,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $owner->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $this->assertTrue($post->canEdit($owner->id));
        $this->assertFalse($post->canEdit($otherUser->id));
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $user->id,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $post->delete();

        $this->assertSoftDeleted('forum_posts', [
            'id' => $post->id,
        ]);
    }

    #[Test]
    public function it_has_editor_relationship(): void
    {
        $author = User::create([
            'name' => 'Author',
            'email' => 'author@example.com',
            'password' => 'password',
        ]);

        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $author->id,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $author->id,
            'content' => 'Original content',
            'number' => 1,
            'edited_user_id' => $editor->id,
            'edited_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $post->editor);
        $this->assertEquals($editor->id, $post->editor->id);
    }
}
