@extends('laravel-forum::shared.layout')
@section('data')
<div class="container posts">
    <div class="row py-3 my-3 border-bottom border-color-secondary">
        <div class="col-auto">
            <a href="{{route('discussions.index')}}" class="h1 text-secondary" >
                <i class="fas fa-chevron-left"></i>
            </a>
        </div>
        <div class="col  text-center">
            <h1 class="text-secondary">{{$discussion->title}}</h1>
            @foreach($discussion->tags as $tag)
                <span class="badge badge" 
                    style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                    {{$tag->name}}
                </span>
            @endforeach
        </div>
    </div>
    @if (session('status'))
    <div class="alert alert-success">

        {{ session('status') }}
    </div>
    @endif
    @forelse($posts as $post)
    <div class="row py-3 my-3 border-bottom border-color-secondary">
        <div class="col-auto">
            <div avatar="{{$post->user->name}}"></div>
        </div>
        <div class="col">
            <div class="text-muted mb-2">
                {{ $post->user->name }}
                {{ $post->created_at->diffForHumans() }}
                &nbsp;
                @if($post->canEdit())
                <span class="dropdown show">
                    <i class="fas fa-ellipsis-v"
                        id="post-options-{{$post->id}}"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        style="cursor: pointer;"></i>
                    <div class="dropdown-menu" aria-labelledby="post-options-{{$post->id}}">
                        @if($post->is_approved)
                        <a class="dropdown-item" href="{{route('posts.status',['post' => $post])}}?key=approve&value=0">
                            <i class="fas fa-times pr-3"></i> Dissaprove
                        </a>
                        @else
                        <a class="dropdown-item" href="{{route('posts.status',['post' => $post])}}?key=approve&value=1">
                            <i class="fas fa-check pr-3"></i> Approve
                        </a>
                        @endif
                        @if($post->hidden_at)
                        <a class="dropdown-item" href="{{route('posts.status',['post' => $post])}}?key=hide&value=1">
                            <i class="fas fa-eye pr-3"></i> Show
                        </a>
                        @else
                        <a class="dropdown-item" href="{{route('posts.status',['post' => $post])}}?key=hide&value=0">
                            <i class="fas fa-eye-slash pr-3"></i>Hide
                        </a>
                        @endif
                        <a class="dropdown-item" href="javascript:void(0)"   onclick="event.preventDefault();toggleEdit({{$post->id}});">
                            <i class="fas fa-edit pr-3"></i> Edit
                        </a>
                        <a class="dropdown-item" href="javascript:void(0)"
                            onclick="event.preventDefault();
                            document.getElementById('delete-post-{{$post->id}}').submit();">
                            <i class="fas fa-trash pr-3"></i> Delete
                        </a>
                        <form id="delete-post-{{$post->id}}" action="{{ route('posts.destroy',['post'=>$post]) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="discussion_id" value="{{$discussion->id}}" />
                            <input type="hidden" name="from" value="discussion">
                        </form>
                    </div>
                </span> 
                @endif
                @if($post->edited_user_id)
                    <br>
                    <small class="text-muted">
                        Edited at {{$post->edited_at->diffForHumans()}}
                        @if ($post->edited_user_id !== $post->user_id)
                            by {{$post->editor->name}}
                        @endif
                    </small>
                @endif  
            </div>
            <div id="post-content-{{$post->id}}">
                {!! nl2br(e($post->content)) !!}
            </div>
            <form action="{{route('posts.update', ['post' => $post])}}" 
                method="POST"
                id="post-form-{{$post->id}}"
                class="d-none">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <input type="hidden" name="discussion_id" value="{{$discussion->id}}" />
                    <input type="hidden" name="from" value="discussion">
                    <textarea class="form-control" 
                        name="content" 
                        id="content" 
                        value="{{old('content')}}" 
                        old="{{$post->content}}"
                        onkeyup="canEdit({{$post->id}})"
                        max-length="100" style="height:200px;">{{$post->content}}</textarea>
                </div>
                <div>
                    <button class="btn btn-default" type="button" onclick="toggleEdit({{$post->id}})">cancel</button>
                    <button class="btn btn-primary" type="submit" disabled="disabled">update</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="row py3 my-3">
        <div class="col  text-center">
            No comments yet.
            @if(!$discussion->is_locked)
                Be the first one!
            @endif
        </div>
    </div>
    @endforelse
    
    <div class="row py-3 my-3">
        @if(!$discussion->is_locked)
        <div class="col-auto">
            <div avatar="{{$discussion->user->name}}"></div>
        </div>
        <div class="col">
            <form action="{{route('posts.store')}}" method="POST" id="post-form">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="discussion_id" value="{{$discussion->id}}" />
                    <input type="hidden" name="from" value="discussion">
                    <textarea class="form-control" 
                        name="content" 
                        id="post-content" 
                        value="{{old('content')}}" 
                        max-length="100" style="height:200px;"></textarea>
                    @if($errors->has('content'))
                    <p class="text-danger">{{$errors->first('content')}}</p>
                    @endif
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Send Answer</button>
                </div>
            </form>
        </div>
        @else
        <div class="col text-center text-muted">
            Discussion locked by owner
        </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    function toggleEdit (postId) {
        var content = document.getElementById('post-content-' + postId);
        var form    = document.getElementById('post-form-' + postId);

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

    function canEdit (postId) {
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