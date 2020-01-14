@extends('layouts.app')
@section('content')
<div class="container">

    @if (session('status'))
    <div class="alert alert-success">

        {{ session('status') }}
    </div>
    @endif
    <h1> Settings </h1>
    <div>
        <a href="{{route('settings.create')}}">New</a>
    </div>
    <table class="table table-striped">
        @if(count($settings))
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Key</th>
                <th>Value</th>
            </tr>

        </thead>
        @endif
        <tbody>
            @forelse($settings as $setting)
            <tr>
                <td>
                    <a href="{{route('settings.show',['setting'=>$setting] )}}">Show</a>
                    <a href="{{route('settings.edit',['setting'=>$setting] )}}">Edit</a>
                    <a href="javascript:void(0)" onclick="event.preventDefault();
                    document.getElementById('delete-setting-{{$setting->id}}').submit();">
                        {{ __('Delete') }}
                    </a>
                    <form id="delete-setting-{{$setting->id}}" action="{{ route('settings.destroy',['setting'=>$setting]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
                <td>{{ $setting->key }}</td>
                <td>{{ $setting->value   }}</td>
            </tr>
            @empty
            <p>No settings</p>
            @endforelse
        </tbody>
    </table>

</div>

@endsection