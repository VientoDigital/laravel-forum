@extends('laravel-forum::'.config('laravel-forum.views.folder').'shared.layout')
@section('data')
<div class="container posts">
    <div class="row py-3 my-3 border-bottom border-color-secondary">
        <div class="col-auto">
            <a href="{{route('discussions.index')}}" class="h1 text-secondary">
                <i class="fas fa-chevron-left"></i>
            </a>
        </div>
        <div class="col  text-center">
            <h1 class="text-secondary">{{$discussion->title}}</h1>
            @foreach($discussion->tags as $tag)
            <span class="badge badge" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                {{$tag->name}}
            </span>
            @endforeach
        </div>
    </div>
    @if (session('laravel-forum-status'))
    <div class="alert alert-success">

        {{ session('laravel-forum-status') }}
    </div>
    @endif
    
    @livewire('forum.comments', ['discussion'=>$discussion,'posts' => $posts])

    <div class="row py-3 my-3">
        @if(!$discussion->is_locked)
        @livewire('forum.comment', ['discussion' => $discussion,'user'=>Auth::user()->name])
        @else
        <div class="col text-center text-muted">
            Discussion locked by owner
        </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    function toggleEdit(postId) {
        var content = document.getElementById('post-content-' + postId);
        var form = document.getElementById('post-form-' + postId);

        var addContent = document.getElementById('post-content');
        var addSubmit = document.querySelectorAll('#post-form [type=submit]')[0];

        if (form.classList.contains('d-none')) {
            content.classList.remove('d-block');
            content.classList.add('d-none');
            form.classList.remove('d-none');
            form.classList.add('d-block');
            addContent.disabled = true;
            addSubmit.disabled = true;
        } else {
            content.classList.remove('d-none');
            content.classList.add('d-block');
            form.classList.remove('d-block');
            form.classList.add('d-none');
            addContent.disabled = false;
            addSubmit.disabled = false;
        }
    }

    function canEdit(postId) {
        var submit = document.querySelectorAll('#post-form-' + postId + ' [type=submit]')[0];
        var textarea = document.querySelectorAll('#post-form-' + postId + ' [name=content]')[0];
        var data = textarea.value.trim();
        var old = textarea.getAttribute('old').trim();

        if (data.length > 0 && data !== old) {
            submit.disabled = false;
        } else {
            submit.disabled = true;
        }
    }
</script>
@endsection