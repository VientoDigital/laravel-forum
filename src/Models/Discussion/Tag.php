<?php

namespace Vientodigital\LaravelForum\Models\Discussion;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'discussion_id', 'tag_id',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('laravel-forum.table_names.discussion_tags', 'discussion_tag'));
    }
}
