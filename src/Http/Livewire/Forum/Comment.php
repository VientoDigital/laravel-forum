<?php

namespace Vientodigital\LaravelForum\Http\Livewire\Forum;

use Livewire\Component;
use Vientodigital\LaravelForum\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vientodigital\LaravelForum\Events\CommentEvent;

class Comment extends Component
{
    public $discussion;
    public $avatar;
    public $content ;

    const COMMENT_UPLOADED = 'commentUploaded';

    public function mount($discussion, $user)
    {
        $this->discussion = $discussion;
        $this->avatar = $user;
    }
    public function render()
    {
        return view('laravel-forum::tw.livewire.forum.comment');
    }
    public function save(Request $request)
    {
        $validatedData = $this->validate([
            'content' => 'required|min:2',
        ]);
        $discussion = $this->discussion;
        $data = [];
        $data['discussion_id'] = $discussion->id;
        $data['content'] = $this->content;
        $data['user_id'] = Auth::user()->id;
        $data['is_private'] = 0;
        $data['is_approved'] = 1;
        $data['number'] = $discussion->post_number_index + 1;
        $data['ip_address'] = $request->ip();
        $post = Post::create($data);

        $discussion->comment_count = ($discussion->comment_count + 1);
        $discussion->participant_count = count($discussion->posts()->get()->unique('user_id'));
        $discussion->post_number_index = $post->number;
        $discussion->last_posted_at = $post->created_at;
        $discussion->last_posted_user_id = $post->user_id;
        $discussion->last_post_id = $post->id;

        if (1 === $post->number) {
            $discussion->first_post_id = $post->id;
        }
        $discussion->save();
        $this->discussion = $discussion;
        $this->content = '';

        $this->emit(self::COMMENT_UPLOADED);
        CommentEvent::dispatch(Auth::user(), $discussion, $post);
    }
}
