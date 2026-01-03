<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Discussion\User as DiscussionUser;
use Vientodigital\LaravelForum\Models\Post;
use Vientodigital\LaravelForum\Models\Tag;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class DiscussionTest extends TestCase
{
    #[Test]
    public function it_can_create_a_discussion(): void
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

        $this->assertDatabaseHas('forum_discussions', [
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
        ]);
    }

    #[Test]
    public function it_uses_correct_table_name(): void
    {
        $discussion = new Discussion();
        $this->assertEquals('forum_discussions', $discussion->getTable());
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $discussion = new Discussion();
        $fillable = $discussion->getFillable();

        $this->assertContains('title', $fillable);
        $this->assertContains('slug', $fillable);
        $this->assertContains('user_id', $fillable);
        $this->assertContains('is_locked', $fillable);
        $this->assertContains('is_sticky', $fillable);
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

        $this->assertInstanceOf(User::class, $discussion->user);
        $this->assertEquals($user->id, $discussion->user->id);
    }

    #[Test]
    public function it_has_many_posts(): void
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

        Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'First post',
            'number' => 1,
        ]);

        Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Second post',
            'number' => 2,
        ]);

        $this->assertCount(2, $discussion->posts);
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

        $this->assertTrue($discussion->canEdit($owner->id));
        $this->assertFalse($discussion->canEdit($otherUser->id));
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

        $discussion->delete();

        $this->assertSoftDeleted('forum_discussions', [
            'id' => $discussion->id,
        ]);
    }

    #[Test]
    public function it_belongs_to_many_tags(): void
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

        $tag = Tag::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $discussion->tags()->attach($tag->id);

        $this->assertCount(1, $discussion->tags);
        $this->assertEquals('General', $discussion->tags->first()->name);
    }

    #[Test]
    public function it_has_last_post_relationship(): void
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

        $firstPost = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'First post',
            'number' => 1,
        ]);
        $firstPost->created_at = now()->subHour();
        $firstPost->save();

        $lastPost = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Last post',
            'number' => 2,
        ]);

        $discussion->refresh();
        $this->assertEquals($lastPost->id, $discussion->lastPost->id);
    }

    #[Test]
    public function it_can_check_if_discussion_is_read(): void
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
            'post_number_index' => 5,
        ]);

        // Not read yet
        $this->assertFalse($discussion->isRead($user->id));

        // Mark as read
        DiscussionUser::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_post_number' => 5,
        ]);

        $this->assertTrue($discussion->isRead($user->id));
    }

    #[Test]
    public function it_returns_false_for_partially_read_discussion(): void
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
            'post_number_index' => 10,
        ]);

        // User has only read up to post 5
        DiscussionUser::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_post_number' => 5,
        ]);

        $this->assertFalse($discussion->isRead($user->id));
    }
}
