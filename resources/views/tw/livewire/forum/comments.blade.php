<div wire:poll.5000ms>
    @forelse($posts as $post)
    <div x-data="{dropdown:false,edit:false}" x-init="edit=false;dropdown=false" class="flex items-start md:mx-4 pb-4">
        <div class="w-16 hidden md:block">
            <div class="bg-primary-500 font-semibold inline-block mt-3 mx-auto p-3 rounded-full text-white">
                {{Str::of($post->user->name)->upper()->initials()}}
            </div>
        </div>
        <div class="bg-gray-100 mt-3 p-3 rounded w-full">
            <div class="">
                <div x-show="!edit" class="text-gray-700" id="post-content-{{$post->id}}">
                    <p class="">
                        <span class="text-primary-500 font-semibold">
                            {{ $post->user->name }}
                        </span>
                        @if(!$post->is_approved)
                        <span class="text-xs text-red-500">
                            (Mensaje Desaprobado)
                        </span>
                        @endif
                        {!! nl2br(e($post->content)) !!}
                    </p>
                </div>
                <div x-show="edit">
                    @livewire('forum.comment-edit', ['post' => $post],key($post->id))
                </div>
            </div>
        </div>
        @if($post->canEdit())
        <div class="relative inline-block text-left mt-3" @click.away="dropdown=false">
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
                        <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" wire:click="status({{$post->id}},'approve',0)">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Dissaprove
                        </a>
                        @else
                        <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" wire:click="status({{$post->id}},'approve',1)">
                            <i class="fas fa-check pr-3"></i> Approve
                        </a>
                        @endif
                        @if($post->hidden_at)
                        <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" wire:click="status({{$post->id}},'hide',1)">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Show
                        </a>
                        @else
                        <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" wire:click="status({{$post->id}},'hide',0)">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                            Hide
                        </a>
                        @endif
                        <a class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" @click="edit=true">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Edit
                        </a>
                        <a wire:click="delete({{$post->id}})" class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" href="javascript:void(0)" href="javascript:void(0)">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="flex">
        <div class="md:w-16 w-0">&nbsp;</div>
        <small class="md:ml-4 ml-1 mt-1 text-gray-500 text-xs">
            @if($post->edited_user_id)
            Edited at {{$post->edited_at->diffForHumans()}}
            @if ($post->edited_user_id !== $post->user_id)
            by {{$post->editor->name}}
            @endif
            @else
            {{ $post->created_at->diffForHumans() }}
            @endif
        </small>
    </div>
    @empty
    <div class="row py-3 my-3">
        <div class="col  text-center">
            No comments yet.
            @if(!$discussion->is_locked)
            Be the first one!
            @endif
        </div>
    </div>
    @endforelse
</div>
