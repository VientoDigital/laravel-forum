<?php

namespace Vientodigital\LaravelForum\Http\Livewire\Forum;

use Livewire\Component;
use Vientodigital\LaravelForum\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vientodigital\LaravelForum\Events\CommentEvent;

class CommentEdit extends Component
{
    public $post;
    public $comment;

    const COMMENT_UPDATED = 'commentUpdated';

    public function mount($post)
    {
        $this->post = $post;
        $this->comment = $post->content;
    }
    public function render()
    {
        return view('laravel-forum::tw.livewire.forum.comment-edit', ['post'=>$this->post]);
    }
    public function update($id)
    {
        $this->post->content = $this->comment;
        $this->post->save();
        $this->emit(self::COMMENT_UPDATED);
        CommentEvent::dispatch(Auth::user(), $this->post->discussion, $this->post, 'updated');
    }
}
