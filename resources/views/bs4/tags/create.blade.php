@extends('layouts.app')
@section('content')
<div class="container">
    <h1> Create Tag </h1>
    @if ($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{route('tags.store')}}" method="POST" id="form">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control" 
                type="text"
                name="name"
                id="name"
                value="{{old('name')}}"
                maxlength="100">
            @if($errors->has('name'))
            <p class="text-danger">{{$errors->first('name')}}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" type="text" name="description" id="description" max-length="500">{{old('description')}}</textarea>
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
                value="{{old('color', '#ffffff')}}" 
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
                value="{{old('background_color', '#222222')}}"
                max-length="100">
            @if($errors->has('background_color'))
            <p class="text-danger">{{$errors->first('background_color')}}</p>
            @endif
        </div>


        <div>
            <button class="btn btn-primary" type="submit">Create</button>
            <a href="{{route('tags.index')}}">Back</a>
        </div>
    </form>
</div>

@endsection