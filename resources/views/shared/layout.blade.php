@extends('layouts.app')
@section('content')
@yield('data')

@include('laravel-forum::shared.scripts.avatar')
@include('laravel-forum::shared.scripts.input-boolean')

@endsection