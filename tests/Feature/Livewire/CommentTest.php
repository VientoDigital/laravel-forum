<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Livewire;

use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Events\CommentEvent;
use Vientodigital\LaravelForum\Http\Livewire\Forum\Comment;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class CommentTest extends TestCase
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

        Livewire::test(Comment::class, [
            'discussion' => $discussion,
            'user' => $user,
        ])->assertStatus(200);
    }

    #[Test]
    public function it_can_save_a_comment(): void
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
            'post_number_index' => 0,
            'comment_count' => 0,
        ]);

        $this->actingAs($user);

        Livewire::test(Comment::class, [
            'discussion' => $discussion,
            'user' => $user,
        ])
            ->set('content', 'This is a test comment')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('commentUploaded');

        $this->assertDatabaseHas('forum_posts', [
            'content' => 'This is a test comment',
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(CommentEvent::class);
    }

    #[Test]
    public function it_validates_content_is_required(): void
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

        Livewire::test(Comment::class, [
            'discussion' => $discussion,
            'user' => $user,
        ])
            ->set('content', '')
            ->call('save')
            ->assertHasErrors(['content' => 'required']);
    }

    #[Test]
    public function it_validates_content_minimum_length(): void
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

        Livewire::test(Comment::class, [
            'discussion' => $discussion,
            'user' => $user,
        ])
            ->set('content', 'a')
            ->call('save')
            ->assertHasErrors(['content' => 'min']);
    }
}
