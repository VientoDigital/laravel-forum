# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package Overview

This is a Laravel package (`vientodigital/laravel-forum`) that provides a drop-in forum system for Laravel applications. It includes discussions, posts/comments, tags, and real-time updates via Livewire.

**Requirements:** PHP ^8.2, Laravel 11.x/12.x, Livewire ^3.0

## Common Commands

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage report
composer test-coverage
```

## Architecture

### Source Structure (`/src`)

- **Models/** - Eloquent models: `Discussion`, `Post`, `Tag`, `Setting`, plus pivot models in `Discussion/`
- **Http/Controllers/** - Standard CRUD controllers for discussions, posts, tags, settings
- **Http/Livewire/Forum/** - Real-time components: `Comments`, `Comment`, `CommentEdit`
- **Events/CommentEvent.php** - Broadcast event dispatched when comments are created/updated
- **Rules/** - Custom validation: `Color`, `Slug`

### Key Files

- `LaravelForumServiceProvider.php` - Registers routes, views, config, migrations
- `LaravelForumFacade.php` - Facade exposing `LaravelForum::routes()`
- `config/config.php` - Table names, user model, view theme, route prefix

### Themes

Two view themes in `/resources/views`:
- `tw/` - Tailwind CSS (default)
- `bs4/` - Bootstrap 4

Configure via `config('laravel-forum.views.folder')`.

### Database

Six migrations create tables with `forum_` prefix. All models use soft deletes.

Key tables: `forum_discussions`, `forum_posts`, `forum_tags`, `forum_settings`, plus pivot tables for discussion-user (read tracking) and discussion-tag relationships.

### Route Registration

Routes are registered via the facade in the consuming application:

```php
use Vientodigital\LaravelForum\LaravelForumFacade as LaravelForum;

Route::middleware(['auth'])->prefix('forum')->group(function () {
    LaravelForum::routes();
});
```

### Namespace

All classes use the `Vientodigital\LaravelForum\` namespace (PSR-4 mapped to `src/`).

Tests use `Vientodigital\LaravelForum\Tests\` namespace (mapped to `tests/`).
