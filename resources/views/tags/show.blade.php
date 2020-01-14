@extends('layouts.app')
@section('content')
<div class="container">
    <h1> Show Tag </h1>

    <div class="form-group">
        <label for="key">Name</label>
        <p>{{$tag->name}}</p>
    </div>
    <div class="form-group">
        <label for="slug">Slug</label>
        <p>{{$tag->slug}}</p>
    </div>

    <div class="form-group">
        <label for="color">Color</label>
        <p>{{$tag->color}}</p>
    </div>

    <div class="form-group">
        <label for="background_color">Background color</label>
        <p>{{$tag->background_color}}</p>
    </div>
    <div class="form-group">
        <label for="demo">Demo</label>
        <p>
            <span class="badge" 
                style="color:{{$tag->color}};background:{{$tag->background_color}}">
                {{ $tag->name }}
            </span>
        </p>
    </div>
    
    <a href="{{route('tags.index')}}">Back</a>
</div>
@endsection