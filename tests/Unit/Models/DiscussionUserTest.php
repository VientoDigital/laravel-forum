<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Discussion\User as DiscussionUser;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class DiscussionUserTest extends TestCase
{
    #[Test]
    public function it_can_create_a_discussion_user_pivot(): void
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

        $discussionUser = DiscussionUser::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_at' => now(),
            'last_read_post_number' => 5,
        ]);

        $this->assertDatabaseHas('forum_discussion_user', [
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_post_number' => 5,
        ]);
    }

    #[Test]
    public function it_uses_correct_table_name(): void
    {
        $discussionUser = new DiscussionUser();
        $this->assertEquals('forum_discussion_user', $discussionUser->getTable());
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $discussionUser = new DiscussionUser();
        $fillable = $discussionUser->getFillable();

        $this->assertContains('discussion_id', $fillable);
        $this->assertContains('user_id', $fillable);
        $this->assertContains('last_read_at', $fillable);
        $this->assertContains('last_read_post_number', $fillable);
    }

    #[Test]
    public function it_tracks_read_position(): void
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

        $discussionUser = DiscussionUser::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_post_number' => 5,
        ]);

        // User has read up to post 5, but discussion has 10 posts
        $this->assertEquals(5, $discussionUser->last_read_post_number);
        $this->assertNotEquals($discussion->post_number_index, $discussionUser->last_read_post_number);
    }

    #[Test]
    public function it_can_update_read_position(): void
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

        $discussionUser = DiscussionUser::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_post_number' => 5,
        ]);

        $discussionUser->update(['last_read_post_number' => 10]);

        $this->assertDatabaseHas('forum_discussion_user', [
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'last_read_post_number' => 10,
        ]);
    }
}
