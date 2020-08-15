<?php

namespace Vientodigital\LaravelForum\Http\Livewire\Forum;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vientodigital\LaravelForum\Models\Post;
use Carbon\Carbon;

class Comments extends Component
{
    public $listeners = [Comment::COMMENT_UPLOADED => 'reload',CommentEdit::COMMENT_UPDATED => 'reload'];
    public $discussion;
    public $comment = [];
    public function mount($discussion,$posts){
        $this->discussion = $discussion;
        $this->comment = [];
    }
    public function render()
    {   
        $data = ['posts'=>$this->getPosts()];
        return view('laravel-forum::tw.livewire.forum.comments',$data);
    }

    public function reload()
    {
        $this->posts=[];
        $this->posts = $this->getPosts();
    }

    protected function getPosts(){
       return ($this->discussion->canEdit())
        ? $this->discussion->posts()->orderBy('created_at', 'ASC')->get()
        : $this->discussion->posts()->where('is_approved', 1)
        ->orderBy('created_at', 'ASC')
        ->get();
    }
    public function delete($id){
        $post = Post::find($id);
        $post->delete();
        $this->posts = $this->getPosts();
    }

    public function status($id,$key,$value){
        $post = Post::find($id);
        if (!$post->canEdit(Auth::user()->id)) {
            // Forbidden
            
        }
        switch ($key) {
            case 'approve':
                $post->is_approved = $value ? 1 : 0;
            break;
            case 'private':
                $post->is_private = true === $value ? 1 : 0;
            break;
            case 'hide':
                if ($value) {
                    $post->hidden_at = null;
                    $post->hidden_user_id = null;
                } else {
                    $post->hidden_at = Carbon::now()->toDateTimeString();
                    $post->hidden_user_id = Auth::user()->id;
                }
            break;
        }
        $post->save();
        $this->posts = $this->getPosts();
    }
    
}