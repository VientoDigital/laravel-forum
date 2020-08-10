@extends('laravel-forum::'.config('laravel-forum.views.folder').'shared.layout')
@section('data')
<div class="container mx-auto mt-8">
    <h1 class="text-xl font-semibold"> Create Discussion </h1>
    @if ($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{route('discussions.store')}}" method="POST" id="form">
        @csrf
        <div class="grid grid-row-2">
            <label class="font-semibold mt-4 cols-span-1" for="title">Title</label>
            <div class="mt-2 cols-span-3">
                <input class="py-2 px-3 border border-gray-300 rounded bg-white w-full" type="text" name="title" id="title" value="{{old('title')}}" maxlength="200">
                @if($errors->has('title'))
                <p class="text-danger">{{$errors->first('title')}}</p>
                @endif
            </div>
        </div>
        <div class="mt-4 font-semibold">

            <div class="form-check form-check-inline">
                <input class="form-check-input" name="is_private" type="checkbox" id="is_private" boolean value="{{old('is_private', '0')}}">
                <label class="form-check-label" for="is_private">private </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="is_approved" type="checkbox" id="is_approved" boolean value="{{old('is_approved', '1')}}">
                <label class="form-check-label" for="is_approved">approved </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="is_locked" type="checkbox" id="is_locked" boolean value="{{old('is_locked', '0')}}">
                <label class="form-check-label" for="is_locked">locked </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="is_sticky" type="checkbox" id="is_sticky" boolean value="{{old('is_sticky', '0')}}">
                <label class="form-check-label" for="is_sticky">sticky </label>
            </div>
        </div>
        <div class="mt-4">
            <h2>
                Tags (
                <span id="tag-counter">
                    {{ is_array(old('tags')) ? count(old('tags')) : 0 }}
                </span>
                )
            </h2>
        </div>

        <div class="form-group">
            @foreach($tags as $tag)
            <span class="badge" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="tags[{{$tag->id}}]" tag-checkbox type="checkbox" id="tags-{{$tag->id}}" {{old('tags.'.$tag->id, '0') === (string)$tag->id ? 'checked="checked"' : ''}} value="{{$tag->id}}" onclick="count_tags()">
                    <label class="form-check-label" for="tags-{{$tag->id}}">{{$tag->name}}</label>
                </div>
            </span>
            @endforeach
        </div>

        <div>
            <button class="btn btn-primary" type="submit">Create</button>
            <a href="{{route('discussions.index')}}">Back</a>
        </div>
    </form>
</div>

<script type="text/javascript">
    function count_tags() {
        var el = document.querySelectorAll('#form input[type=checkbox][tag-checkbox]');
        var count = 0;

        for (var i = 0; i < el.length; i++) {
            if (el[i].checked === true) {
                count++;
            }
        }
        document.getElementById('tag-counter').innerHTML = count;
    }
    count_tags();
</script>
@endsection