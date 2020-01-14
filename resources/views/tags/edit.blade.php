@extends('layouts.app')
@section('content')
<div class="container">
    <h1> Edit Tag </h1>
    @if ($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif


    <form action="{{route('tags.update',['tag'=>$tag])}}" method="POST" id="form">
        @method('PUT')
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control" 
                type="text"
                name="name"
                id="name"
                value="{{old('name', $tag->name)}}"
                maxlength="100">
            @if($errors->has('name'))
            <p class="text-danger">{{$errors->first('name')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" type="text" name="description" id="description" max-length="500">{{old('description', $tag->description)}}</textarea>
            @if($errors->has('description'))
            <p class="text-danger">{{$errors->first('description')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="color">Color</label>
            <input 
                class="form-control" 
                type="color" 
                name="color" 
                id="color" 
                value="{{old('color', $tag->color)}}" 
                max-length="50">
            @if($errors->has('color'))
            <p class="text-danger">{{$errors->first('color')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="background_color">Background Color</label>
            <input class="form-control" 
                type="color"
                name="background_color"
                id="background_color"
                value="{{old('background_color', $tag->background_color)}}"
                max-length="100">
            @if($errors->has('background_color'))
            <p class="text-danger">{{$errors->first('background_color')}}</p>
            @endif
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Save</button>
            <a href="{{route('tags.index')}}">Back</a>
        </div>

    </form>
</div>

@endsection