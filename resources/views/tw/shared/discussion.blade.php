<?php
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Discussion\User as DiscussionUser;
use Carbon\Carbon;
use Illuminate\Support\Str;
    // If discussion has not defined directly, we can find it
    // with slug var.
    if (!isset($discussion)) {
        $discussion = Discussion::where('slug', $slug)->first();
        // If doesnt exists, we create it
        if (!$discussion) {
            //If no config, we set common config.
            if (!isset($config) || is_array($config)) {
                $config = [
                    'is_private' => 0,
                    'is_approved' => 1,
                    'is_sticky' => 0,
                    'is_locked' => 0,
                ];
            }
            // If no user_id, we set current user as owner
            if (!isset($config['user_id'])) {
                $config['user_id'] = Auth::user()->id;
            }
            // If no title, we revert slug to human words
            if (!isset($config['title'])) {
                $config['title'] = str_replace('-', ' ', $slug);
            }
            // Sets slug
            $config['slug'] = $slug;
            // Now we can create the discussion
            $discussion = Discussion::create($config);
        }
    }

    //Show all posts if user is owner, otherwise, hide unnaproved posts
    $posts = ($discussion->canEdit())
        ? $discussion->posts()->orderBy('created_at', 'ASC')->get()
        : $discussion->posts()->where('is_approved', 1)
            ->orderBy('created_at', 'ASC')
            ->get()
        ;
    //Store current user "read"
    $discussionUser = DiscussionUser::where('user_id', Auth::user()->id)
        ->where('discussion_id', $discussion->id)
        ->first()
    ;
    if (!$discussionUser) {
        $discussionUser = new DiscussionUser();
        $discussionUser->fill([
            'discussion_id' => $discussion->id,
            'user_id' => Auth::user()->id,
        ]);
    }
    $discussionUser->last_read_at = Carbon::now()->toDateTimeString();
    $discussionUser->last_read_post_number = $discussion->post_number_index;
    $discussionUser->save();
?>
<div class="">
    <div class="mt-2 flex">
        <div>
            <h1 class="capitalize">{{$discussion->title}}</h1>
            @foreach($discussion->tags as $tag)
            <span class="py-1 px-2 rounded-full" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                {{$tag->name}}
            </span>
            @endforeach
        </div>
    </div>
    @if (session('laravel-forum-status'))
    <div class="bg-green-500 text-white px-6 py-2 rounded">
        {{ session('laravel-forum-status') }}
    </div>
    @endif
    @forelse($posts as $post)
    <div class="py-4 px-4 my-3 bg-gray-100 rounded">
        <div class="flex items-center justify-between">
            <div class="bg-primary-500 font-semibold inline-block p-3 rounded-full text-white" avatar="{{$post->user->name}}"></div>
            <div class="text-gray-500 mb-2 text-xs flex items-center">
                {{ $post->user->name }}
                {{ $post->created_at->diffForHumans() }}
                &nbsp;
                @if($post->canEdit())
                <div class="relative inline-block text-left" x-data="{dropdown:false}" @click.away="dropdown=false">
                    <div>
                        <button @click="dropdown=!dropdown" class="flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                    </div>

                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg" x-show="dropdown">
                        <div class="rounded-md bg-white shadow-xs">
                            <div class="py-1">
                                @if($post->is_approved)
                                <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="{{route('posts.status',['post' => $post])}}?key=approve&value=0">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Dissaprove
                                </a>
                                @else
                                <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="{{route('posts.status',['post' => $post])}}?key=approve&value=1">
                                    <i class="fas fa-check pr-3"></i> Approve
                                </a>
                                @endif
                                @if($post->hidden_at)
                                <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="{{route('posts.status',['post' => $post])}}?key=hide&value=1">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Show
                                </a>
                                @else
                                <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="{{route('posts.status',['post' => $post])}}?key=hide&value=0">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                    Hide
                                </a>
                                @endif
                                <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="javascript:void(0)" onclick="event.preventDefault();toggleEdit({{$post->id}});">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="javascript:void(0)" href="javascript:void(0)" onclick="event.preventDefault();
                            document.getElementById('delete-post-{{$post->id}}').submit();">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </a>
                                <form id="delete-post-{{$post->id}}" action="{{ route('posts.destroy',['post'=>$post]) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="discussion_id" value="{{$discussion->id}}" />
                                    <input type="hidden" name="from" value="discussion">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @endif
                @if($post->edited_user_id)
                <br>
                <small class="text-gray-500">
                    Edited at {{$post->edited_at->diffForHumans()}}
                    @if ($post->edited_user_id !== $post->user_id)
                    by {{$post->editor->name}}
                    @endif
                </small>
                @endif
            </div>
        </div>
        <div class="mt-4">

            <div class="mt-4 text-gray-800" id="post-content-{{$post->id}}">
                <p class="py-2 px-4">
                    {!! nl2br(e($post->content)) !!}
                </p>
            </div>
            <form action="{{route('posts.update', ['post' => $post])}}" method="POST" id="post-form-{{$post->id}}" class="hidden">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <input type="hidden" name="discussion_id" value="{{$discussion->id}}" />
                    <input type="hidden" name="from" value="discussion">
                    <textarea class="bg-white border border-gray-300 focus:outline-none focus:shadow-outline mt-4 px-3 py-2 rounded text-gray-800 w-full" name="content" id="content" value="{{old('content')}}" old="{{$post->content}}" onkeyup="canEdit({{$post->id}})" max-length="100" style="height:200px;">{{$post->content}}</textarea>
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

    <div class="flex justify-between items-base py-3 my-3">
        @if(!$discussion->is_locked)
        <div class="mt-4">
            <div class="bg-primary-500 font-semibold inline-block p-3 rounded-full text-white" avatar="{{$discussion->user->name}}"></div>
        </div>
        <div class="mt-4 ml-4 w-full">
            <form action="{{route('posts.store')}}" method="POST" id="post-form">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="discussion_id" value="{{$discussion->id}}" />
                    <input type="hidden" name="from" value="{{Route::currentRouteName()}}">
                    <textarea placeholder="Comenta aqui" class=" w-full border border-gray-200 active:outline-none focus:outline-none focus:shadow-outline-blue p-3 placeholder-gray-500 rounded" name="content" id="post-content" value="{{old('content')}}"></textarea>
                    @if($errors->has('content'))
                    <p class=" text-danger">{{$errors->first('content')}}</p>
                    @endif
                </div>
                <div class="flex justify-end">
                    <button class="mt-4 py-2 px-4 bg-primary-500 rounded text-white" type="submit">Send Answer</button>
                </div>
            </form>
        </div>
        @else
        <div class="col text-center text-gray-500">
            Discussion locked by owner
        </div>
        @endif
    </div>
</div>

@include('laravel-forum::'.config('laravel-forum.views.folder').'shared.scripts.avatar')
@include('laravel-forum::'.config('laravel-forum.views.folder').'shared.scripts.input-boolean')
<script type="text/javascript">
    function toggleEdit(postId) {
        var content = document.getElementById('post-content-' + postId);
        var form = document.getElementById('post-form-' + postId);

        var addContent = document.getElementById('post-content');
        var addSubmit = document.querySelectorAll('#post-form [type=submit]')[0];

        if (form.classList.contains('hidden')) {
            content.classList.remove('block');
            content.classList.add('hidden');
            form.classList.remove('hidden');
            form.classList.add('block');
            addContent.disabled = true;
            addSubmit.disabled = true;
        } else {
            content.classList.remove('hidden');
            content.classList.add('block');
            form.classList.remove('block');
            form.classList.add('hidden');
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