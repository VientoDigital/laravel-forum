<?php

namespace Vientodigital\LaravelForum\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;

class CommentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $user;
    public $discussion;
    public $post;
    public $action = 'created';
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user, Discussion $discussion, Post $post, $action = 'created')
    {
        $this->user = $user;
        $this->discussion = $discussion;
        $this->post = $post;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
