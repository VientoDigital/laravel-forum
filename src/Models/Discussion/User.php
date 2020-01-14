<?php

namespace Vientodigital\LaravelForum\Models\Discussion;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'discussion_id', 'user_id', 'last_read_at', 'last_read_post_number',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('laravel-forum.table_names.discussion_users', 'discussion_user'));
    }
}
