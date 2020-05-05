<?php

namespace Vientodigital\LaravelForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'discussion_id', 'number', 'user_id', 'content', 'edited_at', 'edited_user_id',
        'hidden_at', 'hidden_user_id', 'ip_address', 'is_private', 'is_approved',
    ];

    protected $dates = [
        'hidden_at',
        'edited_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('laravel-forum.table_names.posts', 'posts'));
    }

    public function user()
    {
        return $this->belongsTo(config('laravel-forum.models.user', 'App\User'), 'user_id', 'id');
    }

    public function discussion()
    {
        return $this->belongsTo('\Vientodigital\LaravelForum\Models\Discussion', 'discussion_id', 'id');
    }

    public function editor()
    {
        return $this->belongsTo(config('laravel-forum.models.user', 'App\User'), 'edited_user_id', 'id');
    }

    /**
     * Determines if some user can edit current Post.
     *
     * @param int|string $userId if not defined it takes current user automatically from
     *                           Auth facade
     *
     * @return bool
     */
    public function canEdit($userId = null)
    {
        if (!$userId && Auth::user()) {
            $userId = Auth::user()->id;
        }

        if (is_string($userId)) {
            $user = intval($userId);
        }
        // If user is post/discussion owner returns true, otherwise false.
        return $this->user_id === $userId;// || $this->discussion->canEdit($userId);
    }
}
