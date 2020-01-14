<?php

namespace Vientodigital\LaravelForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'background_color',
        'discussion_count', 'last_posted_at', 'last_posted_discussion_id',
        'last_posted_user_id', ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('laravel-forum.table_names.tags', 'tags'));
    }
}
