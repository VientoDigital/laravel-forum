<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Livewire;

use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Events\CommentEvent;
use Vientodigital\LaravelForum\Http\Livewire\Forum\CommentEdit;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class CommentEditTest extends TestCase
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

        $post = Post::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'content' => 'Original content',
            'number' => 1,
        ]);

        Livewire::test(CommentEdit::class, [
            'post' => $post,
        ])->assertStatus(200);
    }

    #[Test]
    public function it_initializes_with_post_content(): void
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

        Livewire::test(CommentEdit::class, [
            'post' => $post,
        ])
            ->assertSet('comment', 'Original content');
    }

    #[Test]
    public function it_can_update_a_comment(): void
    {
        Event::fake();

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

        $this->actingAs($user);

        Livewire::test(CommentEdit::class, [
            'post' => $post,
        ])
            ->set('comment', 'Updated content')
            ->call('update', $post->id)
            ->assertDispatched('commentUpdated');

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'content' => 'Updated content',
        ]);

        Event::assertDispatched(CommentEvent::class, function ($event) {
            return $event->action === 'updated';
        });
    }
}
