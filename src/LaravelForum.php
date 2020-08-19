<?php

namespace Vientodigital\LaravelForum;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LaravelForum
{
    public function routes()
    {
        Route::name(config('laravel-forum.name_prefix'))->group(function () {
            App::make('router')->get('discussions/status', '\Vientodigital\LaravelForum\Http\Controllers\DiscussionController@statusAll')->name('discussions.status.all');
            App::make('router')->get('discussions/{discussion}/status', '\Vientodigital\LaravelForum\Http\Controllers\DiscussionController@status')->name('discussions.status');
            App::make('router')->get('posts/{post}/status', '\Vientodigital\LaravelForum\Http\Controllers\PostController@status')->name('posts.status');
            App::make('router')->get('/', '\Vientodigital\LaravelForum\Http\Controllers\DiscussionController@index')->name('forum.index');
            App::make('router')->resource('discussions', '\Vientodigital\LaravelForum\Http\Controllers\DiscussionController');
            App::make('router')->resource('settings', '\Vientodigital\LaravelForum\Http\Controllers\SettingController');
            App::make('router')->resource('tags', '\Vientodigital\LaravelForum\Http\Controllers\TagController');
            App::make('router')->resource('posts', '\Vientodigital\LaravelForum\Http\Controllers\PostController');

            // Blog demo, to delete
            App::make('router')->get('blog/mi-prueba', function () {
                return view('laravel-forum::' . config('laravel-forum.views.folder') . 'demo.blog');
            })->name('blog.demo');
        });
    }

    public function apiRoutes()
    {
        App::make('router')->apiResource('settings', '\Vientodigital\LaravelForum\Http\Controllers\API\SettingController');
    }
}
