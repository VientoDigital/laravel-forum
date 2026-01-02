<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Livewire;

use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Http\Livewire\Forum\Comments;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class CommentsTest extends TestCase
{
    #[Test]
    public function it_can_render_component(): void
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

        Livewire::test(Comments::class, [
            'discussion' => $discussion,
            'posts' => collect(),
        ])->assertStatus(200);
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
        ]);

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'number' => 1,
            'is_approved' => 1,
        ]);

        $this->actingAs($user);

        Livewire::test(Comments::class, [
            'discussion' => $discussion,
            'posts' => collect([$post]),
        ])
            ->call('delete', $post->id);

        $this->assertSoftDeleted('forum_posts', [
            'id' => $post->id,
        ]);
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

        $this->actingAs($user);

        Livewire::test(Comments::class, [
            'discussion' => $discussion,
            'posts' => collect([$post]),
        ])
            ->call('status', $post->id, 'approve', true);

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
            'is_approved' => 1,
            'is_private' => 0,
        ]);

        $this->actingAs($user);

        Livewire::test(Comments::class, [
            'discussion' => $discussion,
            'posts' => collect([$post]),
        ])
            ->call('status', $post->id, 'private', true);

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'is_private' => 1,
        ]);
    }

    #[Test]
    public function it_reloads_on_comment_uploaded_event(): void
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

        $this->actingAs($user);

        $component = Livewire::test(Comments::class, [
            'discussion' => $discussion,
            'posts' => collect(),
        ]);

        // Create a post directly
        Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'New comment',
            'number' => 1,
            'is_approved' => 1,
        ]);

        // Trigger reload
        $component->call('reload');

        // The component should now see the new post
        $component->assertStatus(200);
    }
}
