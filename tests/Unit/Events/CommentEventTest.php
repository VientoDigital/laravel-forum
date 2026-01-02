<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Events\CommentEvent;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class CommentEventTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
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

        $event = new CommentEvent($user, $discussion, $post);

        $this->assertInstanceOf(CommentEvent::class, $event);
        $this->assertEquals($user->id, $event->user->id);
        $this->assertEquals($discussion->id, $event->discussion->id);
        $this->assertEquals($post->id, $event->post->id);
        $this->assertEquals('created', $event->action);
    }

    #[Test]
    public function it_accepts_custom_action(): void
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

        $event = new CommentEvent($user, $discussion, $post, 'updated');

        $this->assertEquals('updated', $event->action);
    }

    #[Test]
    public function it_can_be_dispatched(): void
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
            'content' => 'Test content',
            'number' => 1,
        ]);

        CommentEvent::dispatch($user, $discussion, $post);

        Event::assertDispatched(CommentEvent::class, function ($event) use ($user, $discussion, $post) {
            return $event->user->id === $user->id
                && $event->discussion->id === $discussion->id
                && $event->post->id === $post->id;
        });
    }
}
