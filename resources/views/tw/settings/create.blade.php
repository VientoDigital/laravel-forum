@extends('layouts.app')
@section('content')
<div class="container">
    <h1> Settings Create </h1>
    @if ($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>

    @endif

    <form action="{{route('settings.store')}}" method="POST">
        @csrf
        <div class="form-group">
            <label for="key">Key</label>
            <input class="form-control" type="text" name="key" id="key" value="{{old('key')}}" maxlength="100">
            @if($errors->has('key'))
            <p class="text-danger">{{$errors->first('key')}}</p>
            @endif
        </div>
        <div class="form-group">

            <label for="value">Value</label>
            <input class="form-control" type="text" name="value" id="value" value="{{old('value')}}">
            @if($errors->has('value'))
            <p class="text-danger">{{$errors->first('value')}}</p>
            @endif
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Create</button>
            <a href="{{route('settings.index')}}">Back</a>
        </div>
    </form>

</div>
@endsection