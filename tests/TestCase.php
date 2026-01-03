<?php

namespace Vientodigital\LaravelForum\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Vientodigital\LaravelForum\LaravelForum;
use Vientodigital\LaravelForum\LaravelForumServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function setUpRoutes(): void
    {
        Route::middleware(['web'])->group(function () {
            (new LaravelForum())->routes();
        });

        Route::middleware(['api'])->prefix('api')->group(function () {
            (new LaravelForum())->apiRoutes();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LaravelForumServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $app['config']->set('laravel-forum.table_names', [
            'settings' => 'forum_settings',
            'discussions' => 'forum_discussions',
            'discussion_users' => 'forum_discussion_user',
            'posts' => 'forum_posts',
            'tags' => 'forum_tags',
            'discussion_tags' => 'forum_discussion_tag',
        ]);

        $app['config']->set('laravel-forum.models.user', 'Vientodigital\LaravelForum\Tests\Fixtures\User');
    }
}
