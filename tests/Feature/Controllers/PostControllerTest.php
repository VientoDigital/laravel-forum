<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class PostControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoutes();
    }

    #[Test]
    public function it_can_store_a_post(): void
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
            'post_number_index' => 0,
            'comment_count' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.store'), [
                'discussion_id' => $discussion->id,
                'content' => 'This is a test post content',
                'from' => 'discussion',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_posts', [
            'discussion_id' => $discussion->id,
            'content' => 'This is a test post content',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_updates_discussion_counters_when_storing_post(): void
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
            'post_number_index' => 0,
            'comment_count' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.store'), [
                'discussion_id' => $discussion->id,
                'content' => 'This is a test post content',
            ]);

        $discussion->refresh();
        $this->assertEquals(1, $discussion->comment_count);
        $this->assertEquals(1, $discussion->post_number_index);
    }

    #[Test]
    public function it_sets_first_post_id_for_first_post(): void
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
            'post_number_index' => 0,
            'comment_count' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('forum.posts.store'), [
                'discussion_id' => $discussion->id,
                'content' => 'First post content here',
            ]);

        $discussion->refresh();
        $post = Post::where('discussion_id', $discussion->id)->first();
        $this->assertEquals($post->id, $discussion->first_post_id);
    }

    #[Test]
    public function it_validates_content_when_storing(): void
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
            ->post(route('forum.posts.store'), [
                'discussion_id' => $discussion->id,
                'content' => 'ab',
                'from' => 'discussion',
            ])
            ->assertSessionHasErrors('content');
    }

    #[Test]
    public function it_can_update_a_post(): void
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
            'content' => 'Original content',
            'number' => 1,
        ]);

        $this->actingAs($user)
            ->put(route('forum.posts.update', ['post' => $post]), [
                'content' => 'Updated content here',
                'from' => 'discussion',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'content' => 'Updated content here',
        ]);
    }

    #[Test]
    public function it_sets_edited_metadata_when_updating(): void
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
            'content' => 'Original content',
            'number' => 1,
        ]);

        $this->actingAs($user)
            ->put(route('forum.posts.update', ['post' => $post]), [
                'content' => 'Updated content here',
            ]);

        $post->refresh();
        $this->assertEquals($user->id, $post->edited_user_id);
        $this->assertNotNull($post->edited_at);
    }

    #[Test]
    public function it_can_delete_a_post(): void
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
            'comment_count' => 1,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $this->actingAs($user)
            ->delete(route('forum.posts.destroy', ['post' => $post]), [
                'from' => 'discussion',
            ])
            ->assertRedirect();

        $this->assertSoftDeleted('forum_posts', [
            'id' => $post->id,
        ]);
    }

    #[Test]
    public function it_decrements_comment_count_when_deleting(): void
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
            'comment_count' => 2,
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
        ]);

        $this->actingAs($user)
            ->delete(route('forum.posts.destroy', ['post' => $post]));

        $discussion->refresh();
        $this->assertEquals(1, $discussion->comment_count);
    }

    #[Test]
    public function it_can_approve_a_post(): void
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
            'is_approved' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('forum.posts.status', ['post' => $post]) . '?key=approve&value=1')
            ->assertRedirect();

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'is_approved' => 1,
        ]);
    }

    #[Test]
    public function it_can_set_post_as_private(): void
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
            'is_private' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('forum.posts.status', ['post' => $post]) . '?key=private&value=1')
            ->assertRedirect();

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'is_private' => 1,
        ]);
    }

    #[Test]
    public function it_can_hide_a_post(): void
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

        $this->actingAs($user)
            ->get(route('forum.posts.status', ['post' => $post]) . '?key=hide&value=0')
            ->assertRedirect();

        $post->refresh();
        $this->assertNotNull($post->hidden_at);
        $this->assertEquals($user->id, $post->hidden_user_id);
    }

    #[Test]
    public function it_can_unhide_a_post(): void
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
            'hidden_at' => now(),
            'hidden_user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('forum.posts.status', ['post' => $post]) . '?key=hide&value=1')
            ->assertRedirect();

        $post->refresh();
        $this->assertNull($post->hidden_at);
        $this->assertNull($post->hidden_user_id);
    }
}
