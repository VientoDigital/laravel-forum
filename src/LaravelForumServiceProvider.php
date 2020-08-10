<?php

namespace Vientodigital\LaravelForum;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Vientodigital\LaravelForum\Http\Livewire\Forum\Comment;
use Vientodigital\LaravelForum\Http\Livewire\Forum\Comments;

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
        Livewire::component('forum.comments', Comments::class);
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
