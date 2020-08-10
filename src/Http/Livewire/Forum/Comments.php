<?php

namespace Vientodigital\LaravelForum\Http\Livewire\Forum;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Comments extends Component
{
    public $listeners = [Comment::COMMENT_UPLOADED => 'commentUploaded'];
    public $discussion;
    public function mount($discussion,$posts){
        $this->discussion = $discussion;
    }
    public function render()
    {   
        $data = ['posts'=>$this->getPosts()];
        return view('laravel-forum::tw.livewire.forum.comments',$data);
    }

    public function commentUploaded()
    {
        $this->posts = $this->getPosts();
    }

    protected function getPosts(){
       return ($this->discussion->canEdit())
        ? $this->discussion->posts()->orderBy('created_at', 'ASC')->get()
        : $this->discussion->posts()->where('is_approved', 1)
        ->orderBy('created_at', 'ASC')
        ->get();
    }
}