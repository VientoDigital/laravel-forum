@extends('layouts.app')
@section('content')
<div class="container">

    @if (session('laravel-forum-status'))
    <div class="alert alert-success">

        {{ session('laravel-forum-status') }}
    </div>
    @endif
    <h1> Tags </h1>
    <div>
        <a href="{{route('tags.create')}}">New</a>
    </div>
    <table class="table table-striped">
        @if(count($tags))
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Name</th>
                <th>Slug</th>
            </tr>
        </thead>
        @endif
        <tbody>
            @forelse($tags as $tag)
            <tr>
                <td>
                    <a href="{{route('tags.show',['tag'=>$tag] )}}">Show</a>
                    <a href="{{route('tags.edit',['tag'=>$tag] )}}">Edit</a>
                    <a href="javascript:void(0)" onclick="event.preventDefault();
                    document.getElementById('delete-tag-{{$tag->id}}').submit();">
                        {{ __('Delete') }}
                    </a>
                    <form id="delete-tag-{{$tag->id}}" action="{{ route('tags.destroy',['tag'=>$tag]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
                <td><span class="badge" style="color:{{$tag->color}};background:{{$tag->background_color}}">
                        {{ $tag->name }}
                    </span>
                </td>
                <td>{{ $tag->slug   }}</td>
            </tr>
            @empty
            <p>No tags</p>
            @endforelse
        </tbody>
    </table>

</div>

@endsection