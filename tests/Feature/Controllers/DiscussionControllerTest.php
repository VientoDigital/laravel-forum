<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Discussion\User as DiscussionUser;
use Vientodigital\LaravelForum\Models\Tag;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class DiscussionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoutes();
    }

    #[Test]
    public function it_can_store_a_discussion(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.discussions.store'), [
                'title' => 'New Discussion',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_discussions', [
            'title' => 'New Discussion',
            'user_id' => $user->id,
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
            ->post(route('forum.discussions.store'), [
                'title' => 'My New Discussion',
            ]);

        $discussion = Discussion::where('title', 'My New Discussion')->first();
        $this->assertNotNull($discussion);
        $this->assertStringContainsString('my-new-discussion', $discussion->slug);
    }

    #[Test]
    public function it_validates_title_when_storing(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.discussions.store'), [
                'title' => '',
            ])
            ->assertSessionHasErrors('title');
    }

    #[Test]
    public function it_can_store_discussion_with_tags(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $tag = Tag::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $this->actingAs($user)
            ->post(route('forum.discussions.store'), [
                'title' => 'Tagged Discussion',
                'tags' => [$tag->id],
            ])
            ->assertRedirect();

        $discussion = Discussion::where('title', 'Tagged Discussion')->first();
        $this->assertCount(1, $discussion->tags);
    }

    #[Test]
    public function it_can_update_a_discussion(): void
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

        $this->actingAs($user)
            ->put(route('forum.discussions.update', ['discussion' => $discussion]), [
                'title' => 'Updated Discussion',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_discussions', [
            'id' => $discussion->id,
            'title' => 'Updated Discussion',
        ]);
    }

    #[Test]
    public function it_generates_new_slug_when_title_changes(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Original Title',
            'slug' => 'original-title-1',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->put(route('forum.discussions.update', ['discussion' => $discussion]), [
                'title' => 'New Title',
            ]);

        $discussion->refresh();
        $this->assertStringContainsString('new-title', $discussion->slug);
    }

    #[Test]
    public function it_can_delete_a_discussion(): void
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

        $this->actingAs($user)
            ->delete(route('forum.discussions.destroy', ['discussion' => $discussion]))
            ->assertRedirect();

        $this->assertSoftDeleted('forum_discussions', [
            'id' => $discussion->id,
        ]);
    }

    #[Test]
    public function it_can_lock_a_discussion(): void
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
            'is_locked' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('forum.discussions.status', ['discussion' => $discussion]) . '?key=lock&value=1')
            ->assertRedirect();

        $this->assertDatabaseHas('forum_discussions', [
            'id' => $discussion->id,
            'is_locked' => 1,
        ]);
    }

    #[Test]
    public function it_can_set_discussion_as_private(): void
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
            'is_private' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('forum.discussions.status', ['discussion' => $discussion]) . '?key=private&value=1')
            ->assertRedirect();

        $this->assertDatabaseHas('forum_discussions', [
            'id' => $discussion->id,
            'is_private' => 1,
        ]);
    }

    #[Test]
    public function it_can_mark_discussion_as_read(): void
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

        $this->actingAs($user)
            ->get(route('forum.discussions.status', ['discussion' => $discussion]) . '?key=read&value=1')
            ->assertRedirect();

        $this->assertDatabaseHas('forum_discussion_user', [
            'user_id' => $user->id,
            'discussion_id' => $discussion->id,
            'last_read_post_number' => 5,
        ]);
    }

    #[Test]
    public function it_can_mark_all_discussions_as_read(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion1 = Discussion::create([
            'title' => 'Discussion 1',
            'slug' => 'discussion-1',
            'user_id' => $user->id,
            'post_number_index' => 3,
        ]);

        $discussion2 = Discussion::create([
            'title' => 'Discussion 2',
            'slug' => 'discussion-2',
            'user_id' => $user->id,
            'post_number_index' => 5,
        ]);

        $this->actingAs($user)
            ->get(route('forum.discussions.status.all') . '?key=read&value=1&ids=' . $discussion1->id . ',' . $discussion2->id)
            ->assertRedirect();

        $this->assertDatabaseHas('forum_discussion_user', [
            'user_id' => $user->id,
            'discussion_id' => $discussion1->id,
        ]);

        $this->assertDatabaseHas('forum_discussion_user', [
            'user_id' => $user->id,
            'discussion_id' => $discussion2->id,
        ]);
    }
}
