@extends('layouts.app')
@section('content')
<div class="container">
    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
</div>

@include('laravel-forum::'.config('laravel-forum.views.folder','bs4').'.shared.discussion', ['slug' => 'mi-prueba'])

@endsection