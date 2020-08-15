<?php

namespace Vientodigital\LaravelForum;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Vientodigital\LaravelForum\Http\Livewire\Forum\Comment;
use Vientodigital\LaravelForum\Http\Livewire\Forum\CommentEdit;
use Vientodigital\LaravelForum\Http\Livewire\Forum\Comments;
use Illuminate\Support\Stringable;


class LaravelForumServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-forum');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-forum');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-forum.php'),
            ], ['config', 'laravel-forum']);

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-forum'),
            ], ['views', 'laravel-forum']);

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], ['migrations', 'laravel-forum']);

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/laravel-forum'),
            ], ['lang', 'laravel-forum']);

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-forum'),
            ], 'assets');*/
        }
        Livewire::component('forum.comment', Comment::class);
        Livewire::component('forum.comment-edit', CommentEdit::class);
        Livewire::component('forum.comments', Comments::class);

        Str::macro('initials', function ($string, $number = 2) {
            $words = preg_split("/[\s,_-]+/", $string);
            $number = (count($words) > $number)?$number:count($words);
            $acronym = '';
            for ($i = 0; $i < $number; $i++) {
                $acronym .= $words[$i][0];
            }

            return $acronym;
        });
        Stringable::macro('initials', function ($number = 2) {
            return new static(Str::initials($this->value, $number));
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-forum');

        $this->app->singleton('laravel-forum', function () {
            return new LaravelForum();
        });
    }
}
